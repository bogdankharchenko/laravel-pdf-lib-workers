<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Tests;

use BackedEnum;
use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Support\Encoder;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;
use RuntimeException;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\Support\DataConfig;

/**
 * Every operation in the spec has a class that sends its "op" and required fields.
 */
final class OperationsTest extends TestCase
{
    private const NS = 'BogdanKharchenko\\PdfLibWorkers\\';

    /**
     * @return iterable<string, array{string, array<string, mixed>}>
     */
    public static function operations(): iterable
    {
        $spec = json_decode((string) file_get_contents(__DIR__.'/../openapi.json'), true, flags: JSON_THROW_ON_ERROR);
        $schemas = $spec['components']['schemas'];

        foreach ($schemas['Operation']['discriminator']['mapping'] as $op => $ref) {
            yield $op => [$op, $schemas[basename($ref)]];
        }
    }

    /**
     * @param  array<string, mixed>  $schema
     */
    #[DataProvider('operations')]
    public function test_sends_the_op_and_required_fields(string $op, array $schema): void
    {
        $class = self::NS.'Operations\\'.ucfirst($op);
        $this->assertTrue(class_exists($class), "No class for {$op}");
        $this->assertTrue(is_a($class, Operation::class, true), "{$class} is not an Operation");

        $encoded = (array) (new Encoder)->encode($this->build($class));

        $this->assertSame($op, $encoded['op']);
        $this->assertEqualsCanonicalizing($schema['required'], array_keys($encoded));
    }

    public function test_has_no_operation_classes_the_spec_lacks(): void
    {
        $ops = array_keys(iterator_to_array(self::operations()));
        $classes = array_map(fn (string $file): string => lcfirst(basename($file, '.php')), glob(__DIR__.'/../generated/Operations/*.php') ?: []);

        $this->assertEqualsCanonicalizing($ops, $classes);
    }

    /**
     * An instance made with sample values for the required parameters only.
     */
    private function build(string $class): object
    {
        $this->assertTrue(class_exists($class), "No class {$class}");
        $constructor = (new ReflectionClass($class))->getConstructor();
        $required = array_filter($constructor?->getParameters() ?? [], fn (ReflectionParameter $p): bool => ! $p->isOptional());
        $args = array_map(fn (ReflectionParameter $p): mixed => $this->sample($class, $p), $required);

        return new $class(...array_values($args));
    }

    /**
     * @param  class-string  $class
     */
    private function sample(string $class, ReflectionParameter $parameter): mixed
    {
        // A list of data objects, as laravel-data reads it from the docblock.
        $type = is_a($class, Data::class, true) ? app(DataConfig::class)->getDataClass($class)->properties[$parameter->getName()]->type : null;
        if ($type?->kind->isDataCollectable() && $type->dataClass !== null) {
            return [$this->build($type->dataClass)];
        }

        // Built-in types first: every source also takes a string shortcut.
        $declared = $parameter->getType();
        $types = array_filter(
            $declared instanceof ReflectionUnionType ? $declared->getTypes() : [$declared],
            fn ($t): bool => $t instanceof ReflectionNamedType && $t->getName() !== Optional::class,
        );
        usort($types, fn (ReflectionNamedType $a, ReflectionNamedType $b): int => $b->isBuiltin() <=> $a->isBuiltin());
        $first = $types[0] ?? null;

        return match (true) {
            ! $first instanceof ReflectionNamedType => throw new RuntimeException("Untyped parameter \${$parameter->getName()}"),
            $first->getName() === 'string' => 'x',
            $first->getName() === 'float' => 1.5,
            $first->getName() === 'int' => 1,
            $first->getName() === 'bool' => true,
            $first->getName() === 'array' => ['x'],
            is_a($first->getName(), BackedEnum::class, true) => $first->getName()::cases()[0],
            is_a($first->getName(), Data::class, true) => $this->build($first->getName()),
            default => throw new RuntimeException("No sample for {$first->getName()}"),
        };
    }
}
