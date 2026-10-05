<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Generator;

use Nette\PhpGenerator\ClassType;
use Nette\PhpGenerator\Literal;
use Nette\PhpGenerator\Method;
use Nette\PhpGenerator\Parameter;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PhpNamespace;
use Nette\PhpGenerator\PsrPrinter;
use RuntimeException;

/**
 * Turns openapi.json into the package's generated PHP: enums, laravel-data
 * classes, operations, sources, response unions, the endpoint methods and the facade.
 *
 * @phpstan-type Field array{name: string, type: PhpType, required: bool, schema: array<string, mixed>}
 */
final class Generator
{
    private const NS = 'BogdanKharchenko\\PdfMill';

    private const PAYLOAD = self::NS.'\\Contracts\\Payload';

    private const OPERATION = self::NS.'\\Contracts\\Operation';

    private const FIELDS = self::NS.'\\Support\\Fields';

    private const JSON_MAP = self::NS.'\\Support\\JsonMap';

    private const UPLOAD = self::NS.'\\Upload';

    private const FILE_RESPONSE = self::NS.'\\FileResponse';

    private const PENDING_PDF = self::NS.'\\PendingPdf';

    private const ADDS_OPERATIONS = self::NS.'\\AddsOperations';

    private const API_EXCEPTION = self::NS.'\\Exceptions\\ApiException';

    private const JSON_OBJECT = self::NS.'\\Support\\Transformers\\JsonObjectTransformer';

    private const LIST_OR_SCALAR = self::NS.'\\Support\\Casts\\DataListOrScalarCast';

    private const DATA = 'Spatie\\LaravelData\\Data';

    private const OPTIONAL = 'Spatie\\LaravelData\\Optional';

    private const COMPUTED = 'Spatie\\LaravelData\\Attributes\\Computed';

    private const COLLECTION_OF = 'Spatie\\LaravelData\\Attributes\\DataCollectionOf';

    private const WITH_TRANSFORMER = 'Spatie\\LaravelData\\Attributes\\WithTransformer';

    private const WITH_CAST = 'Spatie\\LaravelData\\Attributes\\WithCast';

    /** Methods of Client that endpoint methods must not shadow. */
    private const RESERVED_METHODS = ['post', 'get', 'request', 'checked', 'pathParam'];

    /** PendingPdf's own methods (and Conditionable's): an operation of the same name would be hidden or clash. */
    private const BUILDER_METHODS = ['apply', 'filename', 'linkTtl', 'withoutObjectStreams', 'store', 'file', 'download', 'toResponse', 'body', 'when', 'unless'];

    /** Names for enums the spec nests without a name of their own, keyed by the schema holding them. */
    private const ENUM_NAMES = ['PageSize' => 'PaperSize'];

    /** @var array<string, string> component => kind */
    private array $kinds = [];

    /** @var array<string, string> component => FQCN of the class or enum it becomes */
    private array $classes = [];

    /** @var array<string, string> operation component => its "op" value */
    private array $operationValues = [];

    /** @var array<string, true> */
    private array $requestSide = [];

    /** @var array<string, true> */
    private array $responseSide = [];

    /** @var array<string, array{values: list<string>, description: ?string}> enum FQCN => definition */
    private array $enums = [];

    /** @var array<string, string> sorted values (JSON) => enum FQCN, to reuse identical enums */
    private array $enumsByValues = [];

    /** @var array<string, string> relative path => source */
    private array $files = [];

    private string $header;

    public function __construct(private readonly Spec $spec, string $specHash)
    {
        $this->header = "Generated from openapi.json (pdfmill {$spec->version()}, sha256 ".substr($specHash, 0, 12).").\n"
            ."Do not edit: change the API's spec, copy it here and run `composer generate`.";
    }

    /**
     * @return array<string, string> path relative to generated/ => PHP source
     */
    public function generate(): array
    {
        $this->classify();
        $this->markSides();

        foreach ($this->kinds as $name => $kind) {
            match ($kind) {
                'enum' => $this->registerEnum($this->spec->component($name)['enum'], $this->classes[$name], $this->spec->component($name)['description'] ?? null),
                'object', 'merged' => $this->emitData($name),
                'variants' => $this->emitSource($name),
                'results' => $this->emitResultUnion($name),
                default => null,
            };
        }

        $methods = $this->endpointMethods();
        $this->emitEndpoints($methods);
        $this->emitAddsOperations();
        $this->emitFacade($methods);
        $this->emitEnums();
        $this->assertUniqueShortNames();
        ksort($this->files);

        return $this->files;
    }

    // ---------- analysis ----------

    private function classify(): void
    {
        $requests = [];
        foreach ($this->operations() as $operation) {
            $ref = Spec::refName($operation['requestBody']['content']['application/json']['schema'] ?? null);
            if ($ref !== null) {
                $requests[$ref] = true;
            }
        }

        foreach ($this->spec->component('Operation')['discriminator']['mapping'] as $value => $ref) {
            $this->operationValues[substr($ref, strlen('#/components/schemas/'))] = $value;
        }

        $wrappers = [];
        foreach ($this->spec->components() as $name => $schema) {
            $kind = $this->kindOf($schema, isset($requests[$name]));
            $this->kinds[$name] = $kind;
            if ($kind === 'wrapper') {
                $wrappers[(string) Spec::refName($this->objectBranch($schema))] = $name;
            }
        }

        foreach ($this->kinds as $name => $kind) {
            $fqcn = match ($kind) {
                'enum' => self::NS.'\\Enums\\'.$name,
                'object' => isset($this->operationValues[$name])
                    ? self::NS.'\\Operations\\'.$this->operationShortName($name)
                    : self::NS.'\\Data\\'.$name,
                'variants' => self::NS.'\\'.($wrappers[$name] ?? $name),
                'results', 'merged' => self::NS.'\\Data\\'.$name,
                default => null,
            };
            if ($fqcn !== null) {
                $this->classes[$name] = $fqcn;
            }
        }
    }

    /**
     * @param  array<string, mixed>  $schema
     */
    private function kindOf(array $schema, bool $isRequest): string
    {
        $members = $schema['oneOf'] ?? $schema['anyOf'] ?? null;

        return match (true) {
            isset($schema['enum']) => 'enum',
            $isRequest => 'request',
            isset($schema['discriminator']) => 'operations',
            isset($schema['oneOf']) && $this->allInlineObjects($schema['oneOf']) => 'variants',
            $members !== null && $this->allRefsToObjects($members) => 'results',
            $members !== null && $this->allInlineObjects($members) => 'merged',
            $members !== null && $this->isSourceRef($this->objectBranch($schema)) => 'wrapper',
            $members !== null => 'union',
            ($schema['type'] ?? null) === 'object' && isset($schema['properties']) => 'object',
            default => 'scalar',
        };
    }

    /**
     * @param  array<string, mixed>|null  $schema
     */
    private function isSourceRef(?array $schema): bool
    {
        $name = Spec::refName($schema);

        return $name !== null && isset($this->spec->component($name)['oneOf']) && $this->allInlineObjects($this->spec->component($name)['oneOf']);
    }

    /**
     * The $ref member of a "string or object" union, if it is one.
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>|null
     */
    private function objectBranch(array $schema): ?array
    {
        $members = $schema['anyOf'] ?? $schema['oneOf'] ?? [];
        $refs = array_values(array_filter($members, fn (array $m): bool => isset($m['$ref'])));
        $strings = array_filter($members, fn (array $m): bool => ($m['type'] ?? null) === 'string' && ! isset($m['enum']));

        return count($members) === 2 && count($refs) === 1 && count($strings) === 1 ? $refs[0] : null;
    }

    /**
     * @param  list<array<string, mixed>>  $members
     */
    private function allInlineObjects(array $members): bool
    {
        foreach ($members as $member) {
            if (isset($member['$ref']) || ($member['type'] ?? null) !== 'object') {
                return false;
            }
        }

        return $members !== [];
    }

    /**
     * @param  list<array<string, mixed>>  $members
     */
    private function allRefsToObjects(array $members): bool
    {
        foreach ($members as $member) {
            $name = Spec::refName($member);
            if ($name === null || ($this->spec->component($name)['type'] ?? null) !== 'object') {
                return false;
            }
        }

        return $members !== [];
    }

    /**
     * Records which components requests and responses reach: request classes
     * use Optional for fields they may leave out; response classes use null.
     */
    private function markSides(): void
    {
        foreach ($this->operations() as $operation) {
            $request = $operation['requestBody']['content']['application/json']['schema'] ?? null;
            if ($request !== null) {
                $this->walk($request, $this->requestSide);
            }
            foreach ($operation['responses'] ?? [] as $status => $response) {
                $json = $response['content']['application/json']['schema'] ?? null;
                if ($json !== null && (int) $status < 300) {
                    $this->walk($json, $this->responseSide);
                }
            }
        }
        $this->walk(['$ref' => '#/components/schemas/ErrorResponse'], $this->responseSide);
    }

    /**
     * @param  array<mixed>  $schema
     * @param  array<string, true>  $seen
     */
    private function walk(array $schema, array &$seen): void
    {
        $name = Spec::refName($schema);
        if ($name !== null) {
            if (! isset($seen[$name])) {
                $seen[$name] = true;
                $this->walk($this->spec->component($name), $seen);
            }

            return;
        }
        foreach ($schema as $value) {
            if (is_array($value)) {
                $this->walk($value, $seen);
            }
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function operations(): array
    {
        $operations = [];
        foreach ($this->spec->paths() as $path => $methods) {
            foreach ($methods as $method => $operation) {
                $operations[] = $operation + ['x-path' => $path, 'x-method' => $method];
            }
        }

        return $operations;
    }

    private function operationShortName(string $component): string
    {
        return preg_replace('/Operation$/', '', $component) ?? $component;
    }

    // ---------- types ----------

    /**
     * How a schema looks in PHP. $owner and $property name enums the spec leaves unnamed.
     *
     * @param  array<string, mixed>|bool  $schema
     */
    private function type(array|bool $schema, string $owner, string $property): PhpType
    {
        if (! is_array($schema) || isset($schema['not'])) {
            throw new RuntimeException("{$owner}.{$property}: forbidden or boolean schemas have no PHP type");
        }

        if (($name = Spec::refName($schema)) !== null) {
            return $this->refType($name);
        }

        if (isset($schema['enum'])) {
            $fqcn = $this->registerEnum($schema['enum'], self::NS.'\\Enums\\'.(self::ENUM_NAMES[$owner] ?? $owner.Names::pascal($property)), $schema['description'] ?? null);

            return $this->classType($fqcn, 'string');
        }

        if (isset($schema['const'])) {
            $native = get_debug_type($schema['const']);

            return PhpType::scalar($native, $native === 'string' ? 'string' : 'number', var_export($schema['const'], true));
        }

        if (isset($schema['anyOf']) || isset($schema['oneOf'])) {
            return $this->unionType(array_values(array_map(fn (array $m): PhpType => $this->type($m, $owner, $property), $schema['anyOf'] ?? $schema['oneOf'])));
        }

        $type = $schema['type'] ?? null;

        if (is_array($type)) {
            return $this->unionType(array_values(array_map(fn (string $t): PhpType => $this->type(['type' => $t] + $schema, $owner, $property), $type)));
        }

        return match ($type) {
            'string' => PhpType::scalar('string', 'string'),
            'integer' => PhpType::scalar('int', 'number'),
            'number' => PhpType::scalar('float', 'number'),
            'boolean' => PhpType::scalar('bool', 'bool'),
            'null' => new PhpType('null', 'null', 'null', nullable: true),
            'array' => $this->arrayType($schema, $owner, $property),
            'object' => $this->mapType($schema, $owner, $property),
            null => PhpType::scalar('mixed', 'mixed'),
            default => throw new RuntimeException("{$owner}.{$property}: unsupported type {$type}"),
        };
    }

    private function refType(string $name): PhpType
    {
        $schema = $this->spec->component($name);

        return match ($this->kinds[$name]) {
            'enum' => $this->classType($this->classes[$name], 'string'),
            'object', 'merged', 'variants' => $this->classType($this->classes[$name], 'array'),
            'wrapper' => $this->unionType([PhpType::scalar('string', 'string'), $this->refType((string) Spec::refName($this->objectBranch($schema)))]),
            'operations' => $this->classType(self::OPERATION, 'array'),
            'results' => $this->unionType(array_values(array_map(fn (array $m): PhpType => $this->refType((string) Spec::refName($m)), $schema['anyOf'] ?? $schema['oneOf']))),
            'union' => $this->type(array_diff_key($schema, ['description' => 1]), $name, ''),
            'scalar' => $this->type(array_diff_key($schema, ['description' => 1, 'examples' => 1]), $name, ''),
            default => throw new RuntimeException("Component {$name} ({$this->kinds[$name]}) cannot be used as a field type"),
        };
    }

    private function classType(string $fqcn, string $json): PhpType
    {
        return new PhpType($fqcn, $this->short($fqcn), $json, [$fqcn]);
    }

    private function isDataClass(PhpType $type): bool
    {
        return $type->json === 'array' && count($type->uses) === 1 && $type->native === $type->uses[0];
    }

    /**
     * @param  list<PhpType>  $types
     */
    private function unionType(array $types): PhpType
    {
        $native = [];
        $doc = [];
        $uses = [];
        $nullable = false;
        $members = [];

        foreach ($types as $type) {
            if ($type->native === 'null') {
                $nullable = true;

                continue;
            }
            if ($type->native === 'mixed') {
                return PhpType::scalar('mixed', 'mixed');
            }
            foreach (explode('|', $type->native) as $part) {
                $native[$part] = true;
            }
            $doc[$type->doc] = true;
            $uses = [...$uses, ...$type->uses];
            $nullable = $nullable || $type->nullable;
            $members[] = $type;
        }

        // laravel-data reads a union as raw JSON. That's right for plain values; a list of data objects
        // next to plain values is read with DataListOrScalarCast; anything else involving classes or enums
        // can't be told apart from JSON alone.
        $special = array_values(array_filter($members, fn (PhpType $t): bool => $t->uses !== [] || $t->listOf !== null));
        $listOrScalarOf = null;
        $ambiguous = false;
        if (count($members) > 1 && $special !== []) {
            $scalarOthers = array_filter($members, fn (PhpType $t): bool => $t !== $special[0] && in_array($t->json, ['string', 'number', 'bool'], true));
            if (count($special) === 1 && $special[0]->listOf !== null && count($scalarOthers) === count($members) - 1) {
                $listOrScalarOf = $special[0]->listOf;
            } else {
                $ambiguous = true;
            }
        }

        $maps = array_filter($members, fn (PhpType $t): bool => $t->map);

        return new PhpType(
            implode('|', array_keys($native)),
            implode('|', array_keys($doc)),
            count($members) === 1 ? $members[0]->json : 'mixed',
            array_values(array_unique($uses)),
            $nullable,
            count($members) === 1 ? $members[0]->listOf : null,
            $listOrScalarOf,
            count($members) === 1 && $maps !== [],
            $ambiguous || array_filter($members, fn (PhpType $t): bool => $t->ambiguous) !== [],
        );
    }

    /**
     * @param  array<string, mixed>  $schema
     */
    private function arrayType(array $schema, string $owner, string $property): PhpType
    {
        if (isset($schema['prefixItems'])) {
            $items = array_values(array_map(fn (array $item): PhpType => $this->type($item, $owner, $property), $schema['prefixItems']));

            return new PhpType('array', 'array{'.implode(', ', array_map(fn (PhpType $t): string => $t->doc, $items)).'}', 'array', array_values(array_unique(array_merge(...array_map(fn (PhpType $t): array => $t->uses, $items)))));
        }

        $item = $this->type($schema['items'] ?? [], $owner, $property);

        return new PhpType('array', 'list<'.$item->doc.'>', 'array', $item->uses, listOf: $this->isDataClass($item) ? $item->native : null, ambiguous: $item->ambiguous);
    }

    /**
     * A map: a JSON object with arbitrary keys. Named objects are always $refs.
     *
     * @param  array<string, mixed>  $schema
     */
    private function mapType(array $schema, string $owner, string $property): PhpType
    {
        if (isset($schema['properties'])) {
            throw new RuntimeException("{$owner}.{$property}: give this nested object a component name in the spec");
        }

        $value = $this->type($schema['additionalProperties'] ?? [], $owner, $property);

        // array-key, not string: PHP stores keys such as "0" or "2026" as ints.
        return new PhpType('array', 'array<array-key, '.$value->docOrNull($value->nullable).'>', 'array', $value->uses, map: true, ambiguous: $value->ambiguous || $value->listOf !== null || $this->isDataClass($value));
    }

    // ---------- enums ----------

    /**
     * @param  list<string>  $values
     */
    private function registerEnum(array $values, string $fqcn, ?string $description): string
    {
        $sorted = $values;
        sort($sorted);
        $key = (string) json_encode($sorted);

        if (isset($this->enumsByValues[$key])) {
            return $this->enumsByValues[$key];
        }
        if (isset($this->enums[$fqcn])) {
            throw new RuntimeException("Two different enums would both be named {$fqcn}");
        }

        $this->enums[$fqcn] = ['values' => array_values(array_unique($values)), 'description' => $description];
        $this->enumsByValues[$key] = $fqcn;

        return $fqcn;
    }

    private function emitEnums(): void
    {
        foreach ($this->enums as $fqcn => ['values' => $values, 'description' => $description]) {
            [$file, $namespace] = $this->file($fqcn, []);
            $enum = $namespace->addEnum($this->short($fqcn))->setType('string');
            if ($description !== null) {
                $enum->addComment($this->text($description));
            }
            foreach (Names::enumCases($values, $fqcn) as $value => $case) {
                $enum->addCase($case, $value);
            }
            $this->write($fqcn, $file);
        }
    }

    // ---------- data classes ----------

    /**
     * A laravel-data class: an operation, a request part, a response, or a response
     * that comes in several shapes ("merged": one class, fields not in every shape nullable).
     */
    private function emitData(string $name): void
    {
        $schema = $this->kinds[$name] === 'merged' ? $this->mergedSchema($name) : $this->spec->component($name);
        $fqcn = $this->classes[$name];
        $op = $this->operationValues[$name] ?? null;
        $owner = $op !== null ? $this->operationShortName($name) : $name;
        $isRequest = isset($this->requestSide[$name]);
        $isResponse = isset($this->responseSide[$name]);

        $fields = $this->fields($schema, $owner, skip: $op !== null ? ['op'] : []);
        $uses = [self::DATA, ...array_merge(...array_map(fn (array $f): array => $f['type']->uses, $fields))];
        if ($op !== null) {
            $uses = [...$uses, self::OPERATION, self::COMPUTED];
        }

        [$file, $namespace] = $this->file($fqcn, $uses);
        $class = $namespace->addClass($this->short($fqcn))->setFinal()->setExtends(self::DATA);
        $this->describe($class, $schema, $this->kinds[$name] === 'merged' ? 'Comes in several shapes; fields not in every shape are null when absent.' : null);
        if ($op !== null) {
            $class->addImplement(self::OPERATION);
            $class->addProperty('op')->setType('string')->setReadOnly()->addAttribute(self::COMPUTED)
                ->addComment('Names this step in the operations list: always "'.$op.'".');
        }

        $constructor = $class->addMethod('__construct');
        $docs = [];
        foreach ($fields as $field) {
            $docs[] = $this->property($constructor, $namespace, $field, $isRequest, $isResponse, $name);
        }
        if ($docs !== []) {
            $constructor->addComment(implode("\n", $docs));
        }
        if ($op !== null) {
            $constructor->setBody('$this->op = '.var_export($op, true).';');
        }

        $this->write($fqcn, $file);
    }

    /**
     * A promoted, readonly constructor property with the attributes laravel-data needs.
     * Request fields that may be left out are Optional; response fields that may be missing are null.
     *
     * @param  Field  $field
     */
    private function property(Method $constructor, PhpNamespace $namespace, array $field, bool $isRequest, bool $isResponse, string $class): string
    {
        $type = $field['type'];
        $parameter = $constructor->addPromotedParameter($field['name'])->setReadOnly();
        $doc = $this->signature($parameter, $namespace, $field, $isRequest);

        if ($isRequest && $type->map) {
            $this->use($namespace, self::WITH_TRANSFORMER);
            $this->use($namespace, self::JSON_OBJECT);
            $parameter->addAttribute(self::WITH_TRANSFORMER, [new Literal($this->short(self::JSON_OBJECT).'::class')]);
        }
        if ($isResponse) {
            if ($type->ambiguous) {
                throw new RuntimeException("{$class}.{$field['name']}: a response field typed {$type->native} can't be read from JSON");
            }
            if ($type->listOf !== null) {
                $this->use($namespace, self::COLLECTION_OF);
                $parameter->addAttribute(self::COLLECTION_OF, [new Literal($this->short($type->listOf).'::class')]);
            }
            if ($type->listOrScalarOf !== null) {
                $this->use($namespace, self::WITH_CAST);
                $this->use($namespace, self::LIST_OR_SCALAR);
                $parameter->addAttribute(self::WITH_CAST, [new Literal($this->short(self::LIST_OR_SCALAR).'::class'), new Literal($this->short($type->listOrScalarOf).'::class')]);
            }
        }

        return $doc;
    }

    /**
     * Types a field's parameter and returns its @param line. Request fields that may be
     * left out are Optional, so leaving one out (not sent) differs from null (sent as null);
     * response fields that may be missing are null.
     *
     * @param  Field  $field
     */
    private function signature(Parameter $parameter, PhpNamespace $namespace, array $field, bool $isRequest): string
    {
        $type = $field['type'];
        $null = $type->nullable || (! $field['required'] && ! $isRequest);
        $optional = ! $field['required'] && $isRequest;

        $parameter->setType($optional ? $type->native.'|'.self::OPTIONAL.($type->nullable ? '|null' : '') : $type->nativeOrNull($null));
        if ($optional) {
            $this->use($namespace, self::OPTIONAL);
            $parameter->setDefaultValue(new Literal('new Optional()'));
        } elseif (! $field['required']) {
            $parameter->setDefaultValue(null);
        }

        $doc = $optional ? $type->doc.'|Optional'.($type->nullable ? '|null' : '') : $type->docOrNull($null);
        $text = $this->fieldText($field['schema']);
        if ($optional && $type->nullable) {
            $text = trim($text.' Leave out to not send it; null is sent as null.');
        }

        return rtrim("@param  {$doc}  \${$field['name']}  ".str_replace("\n", ' ', $text));
    }

    /**
     * The trait that gives PendingPdf one method per operation, with the operation's own parameters.
     */
    private function emitAddsOperations(): void
    {
        $operations = [];
        foreach ($this->spec->component('Operation')['discriminator']['mapping'] as $op => $ref) {
            if (in_array($op, self::BUILDER_METHODS, true)) {
                throw new RuntimeException("Operation {$op} clashes with a PendingPdf method");
            }
            $name = (string) Spec::refName(['$ref' => $ref]);
            $schema = $this->spec->component($name);
            $operations[$op] = [$this->classes[$name], $schema, $this->fields($schema, $this->operationShortName($name), skip: ['op'])];
        }

        $uses = [self::OPERATION, ...array_column($operations, 0)];
        foreach ($operations as [, , $fields]) {
            $uses = [...$uses, ...array_merge(...array_map(fn (array $f): array => $f['type']->uses, $fields))];
        }
        [$file, $namespace] = $this->file(self::ADDS_OPERATIONS, $uses);
        $trait = $namespace->addTrait($this->short(self::ADDS_OPERATIONS));
        $trait->addComment('One method per operation, each adding it to the PDF. Used by '.$this->short(self::PENDING_PDF).'.');

        $trait->addMethod('apply')->setPublic()->setAbstract()->setReturnType('static')->setVariadic()
            ->addComment('Adds operations made elsewhere.')
            ->addParameter('operations')->setType(self::OPERATION);

        foreach ($operations as $op => [$class, $schema, $fields]) {
            $method = $trait->addMethod($op)->setReturnType('static');
            $docs = array_map(fn (array $field): string => $this->signature($method->addParameter($field['name']), $namespace, $field, isRequest: true), $fields);
            $method->addComment(trim($this->text($schema['description'] ?? '')."\n\n".implode("\n", $docs)));
            $arguments = array_map(fn (array $field): string => "    {$field['name']}: \${$field['name']},", $fields);
            $method->setBody($fields === []
                ? 'return $this->apply(new '.$this->short($class).'());'
                : 'return $this->apply(new '.$this->short($class)."(\n".implode("\n", $arguments)."\n));");
        }

        $this->write(self::ADDS_OPERATIONS, $file);
    }

    /**
     * One schema for a response that comes in several shapes: every field, required only if in all shapes.
     *
     * @return array<string, mixed>
     */
    private function mergedSchema(string $name): array
    {
        $schema = $this->spec->component($name);
        $properties = [];
        $required = null;
        foreach ($schema['anyOf'] ?? $schema['oneOf'] as $variant) {
            $properties += $variant['properties'];
            $required = $required === null ? $variant['required'] ?? [] : array_values(array_intersect($required, $variant['required'] ?? []));
        }

        return ['type' => 'object', 'properties' => $properties, 'required' => $required ?? [], 'description' => $schema['description'] ?? null];
    }

    /**
     * Picks the class for a response that is one of several named objects, by a key only one of them has.
     */
    private function emitResultUnion(string $name): void
    {
        $schema = $this->spec->component($name);
        $members = array_values(array_map(fn (array $m): string => (string) Spec::refName($m), $schema['anyOf'] ?? $schema['oneOf']));
        $fqcn = $this->classes[$name];
        $classes = array_values(array_map(fn (string $m): string => $this->classes[$m], $members));

        [$file, $namespace] = $this->file($fqcn, $classes);
        $class = $namespace->addClass($this->short($fqcn))->setFinal();
        $this->describe($class, $schema, 'Reads a response as '.implode(' or ', array_map($this->short(...), $classes)).'.');

        $method = $class->addMethod('from')->setStatic()->setReturnType(implode('|', $classes));
        $method->addComment('@param  array<string, mixed>  $data');
        $method->addParameter('data')->setType('array');
        $method->setBody($this->resolverBody($members, '$data'));

        $this->write($fqcn, $file);
    }

    /**
     * A match expression choosing among object components by a required key unique to each.
     *
     * @param  list<string>  $members
     */
    private function resolverBody(array $members, string $data): string
    {
        $arms = [];
        foreach ($members as $i => $member) {
            $short = $this->short($this->classes[$member]);
            $arms[] = $i === array_key_last($members)
                ? "    default => {$short}::from({$data}),"
                : "    array_key_exists('".$this->uniqueRequiredKey($member, $members)."', {$data}) => {$short}::from({$data}),";
        }

        return "return match (true) {\n".implode("\n", $arms)."\n};";
    }

    /**
     * @param  list<string>  $members
     */
    private function uniqueRequiredKey(string $member, array $members): string
    {
        foreach ($this->spec->component($member)['required'] ?? [] as $key) {
            $elsewhere = false;
            foreach ($members as $other) {
                $elsewhere = $elsewhere || ($other !== $member && isset($this->spec->component($other)['properties'][$key]));
            }
            if (! $elsewhere) {
                return $key;
            }
        }

        throw new RuntimeException("{$member} has no required key the other response shapes lack");
    }

    // ---------- sources ----------

    /**
     * A source: exactly one of several kinds (key, url, base64, or a file sent
     * with the request), each with its own static constructor and the shared options.
     */
    private function emitSource(string $name): void
    {
        $schema = $this->spec->component($name);
        $fqcn = $this->classes[$name];
        $owner = $this->short($fqcn);
        $variants = $schema['oneOf'];

        $kinds = [];
        foreach ($variants as $variant) {
            $kinds[$this->variantKind($variant, $variants)] = $variant;
        }
        $shared = array_diff_key(
            array_intersect_key(...array_map(fn (array $v): array => array_filter($v['properties'], fn ($p): bool => $p !== false), $variants)),
            $kinds,
        );

        [$file, $namespace] = $this->file($fqcn, [self::PAYLOAD, self::FIELDS]);
        $class = $namespace->addClass($owner)->setFinal()->setReadOnly()->addImplement(self::PAYLOAD);
        $ways = array_map(fn (string $k): string => $k === 'upload' ? "{$owner}::file(), ::contents(), ::disk()" : "{$owner}::{$k}()", array_keys($kinds));
        $this->describe($class, $schema, 'Create one with '.implode(', ', $ways).'.');

        $constructor = $class->addMethod('__construct')->setPrivate();
        $constructor->addComment('@param  array<string, mixed>  $fields');
        $constructor->addPromotedParameter('fields')->setPrivate()->setType('array');

        foreach ($kinds as $kind => $variant) {
            $extras = array_diff_key(array_filter($variant['properties'], fn ($p): bool => $p !== false), $shared, [$kind => true]);
            $options = $this->fields(['properties' => $extras + $shared, 'required' => []], $owner);
            foreach ($options as $option) {
                foreach ($option['type']->uses as $use) {
                    $this->use($namespace, $use);
                }
                if ($option['type']->map) {
                    $this->use($namespace, self::JSON_MAP);
                }
            }

            if ($kind === 'upload') {
                $this->uploadConstructors($class, $namespace, $options);

                continue;
            }

            $kindField = $this->fields(['properties' => [$kind => $variant['properties'][$kind]], 'required' => [$kind]], $owner);
            $this->sourceMethod($class->addMethod($kind)->setStatic()->setReturnType('self'), [...$kindField, ...$options]);
        }

        $payload = $class->addMethod('payload')->setReturnType('array');
        $payload->addComment('@return array<string, mixed>');
        $payload->setBody('return $this->fields;');

        $this->write($fqcn, $file);
    }

    /**
     * @param  array<string, mixed>  $variant
     * @param  list<array<string, mixed>>  $variants
     */
    private function variantKind(array $variant, array $variants): string
    {
        foreach ($variant['required'] ?? [] as $key) {
            $others = array_filter($variants, fn (array $v): bool => $v !== $variant && ($v['properties'][$key] ?? null) !== false);
            if ($others === []) {
                return $key;
            }
        }

        throw new RuntimeException('A source variant has no required key the others forbid');
    }

    /**
     * file(), contents() and disk(): the ways a Laravel app hands over a file.
     *
     * @param  list<Field>  $options
     */
    private function uploadConstructors(ClassType $class, PhpNamespace $namespace, array $options): void
    {
        $this->use($namespace, self::UPLOAD);
        $this->use($namespace, 'SplFileInfo');
        $upload = $this->short(self::UPLOAD);
        $ways = [
            'file' => ['A file on disk, or one uploaded to your app ($request->file(\'…\')). It is sent with the request.', [['file', 'SplFileInfo|string', false, ''], ['filename', 'string|null', true, 'Name sent with the file. Default: the uploaded or base name.']], "{$upload}::fromFile(\$file, \$filename)"],
            'contents' => ['Bytes you already have in memory. They are sent with the request.', [['contents', 'string', false, ''], ['filename', 'string', false, '']], "{$upload}::fromContents(\$contents, \$filename)"],
            'disk' => ['A file on one of your Laravel filesystem disks. It is sent with the request.', [['path', 'string', false, ''], ['disk', 'string|null', true, 'Default: your default disk.']], "{$upload}::fromDisk(\$path, \$disk)"],
        ];

        foreach ($ways as $name => [$summary, $parameters, $create]) {
            $method = $class->addMethod($name)->setStatic()->setReturnType('self');
            $lines = [$summary, ''];
            foreach ($parameters as [$param, $type, $optional, $doc]) {
                $p = $method->addParameter($param)->setType($type);
                if ($optional) {
                    $p->setDefaultValue(null);
                }
                if ($doc !== '') {
                    $lines[] = "@param  {$type}  \${$param}  {$doc}";
                }
            }
            $this->sourceMethod($method, $options, "'upload' => {$create},", $lines);
        }
    }

    /**
     * @param  list<Field>  $fields
     * @param  list<string>  $lines
     */
    private function sourceMethod(Method $method, array $fields, string $first = '', array $lines = []): void
    {
        $entries = $first === '' ? [] : ["    {$first}"];
        foreach ($fields as $field) {
            $this->parameter($method, $field);
            $lines[] = $this->paramDoc($field);
            $value = '$'.$field['name'];
            $entries[] = "    '{$field['name']}' => ".($field['type']->map ? "is_array({$value}) ? new ".$this->short(self::JSON_MAP)."({$value}) : {$value}" : $value).',';
        }
        $method->addComment(rtrim(implode("\n", $lines)));
        $method->setBody('return new self('.$this->short(self::FIELDS)."::compact([\n".implode("\n", $entries)."\n]));");
    }

    // ---------- fields and parameters ----------

    /**
     * @param  array<string, mixed>  $schema
     * @param  list<string>  $skip
     * @return list<Field>
     */
    private function fields(array $schema, string $owner, array $skip = []): array
    {
        $required = $schema['required'] ?? [];
        $fields = [];
        foreach ($schema['properties'] ?? [] as $name => $property) {
            if (in_array($name, $skip, true) || $property === false) {
                continue;
            }
            $fields[] = [
                'name' => $name,
                'type' => $this->type($property, $owner, $name),
                'required' => in_array($name, $required, true),
                'schema' => $property,
            ];
        }
        usort($fields, fn (array $a, array $b): int => $b['required'] <=> $a['required']);

        return $fields;
    }

    /**
     * A method parameter (sources and endpoints): optional ones default to null and are left out when null.
     *
     * @param  Field  $field
     */
    private function parameter(Method $method, array $field): void
    {
        $parameter = $method->addParameter($field['name'])->setType($field['type']->nativeOrNull(! $field['required'] || $field['type']->nullable));
        if (! $field['required']) {
            $parameter->setDefaultValue(null);
        }
    }

    /**
     * @param  Field  $field
     */
    private function paramDoc(array $field): string
    {
        $type = $field['type']->docOrNull(! $field['required'] || $field['type']->nullable);

        return rtrim("@param  {$type}  \${$field['name']}  ".str_replace("\n", ' ', $this->fieldText($field['schema'])));
    }

    // ---------- endpoints and facade ----------

    /**
     * @return list<array{name: string, summary: string, description: string, params: list<Field>, returns: string, returnDoc: string, uses: list<string>, bodyUses: list<string>, body: string}>
     */
    private function endpointMethods(): array
    {
        $methods = [];
        foreach ($this->operations() as $operation) {
            $name = $operation['operationId'];
            if (in_array($name, self::RESERVED_METHODS, true)) {
                throw new RuntimeException("operationId {$name} clashes with a Client method");
            }
            $ok = $operation['responses']['200']['content'] ?? [];
            $json = $ok['application/json']['schema'] ?? null;
            $typedJson = $json !== null && (Spec::refName($json) !== null || isset($json['oneOf']));
            $binary = isset($ok['application/pdf']) || isset($ok['application/octet-stream']);
            $summary = $operation['summary'] ?? '';
            $description = $operation['description'] ?? '';
            if ($operation['x-method'] === 'head' || (! $typedJson && ! $binary)) {
                continue;
            }

            $request = Spec::refName($operation['requestBody']['content']['application/json']['schema'] ?? null);
            $schema = $request === null ? [] : $this->spec->component($request);
            if (isset($schema['properties']['operations'], $schema['properties']['output'])) {
                $methods[] = $this->builderMethod($operation, $name, (string) $request, $schema);

                continue;
            }
            if ($typedJson && $binary) {
                throw new RuntimeException("{$name} returns JSON or a file but takes no operations: decide how the client should offer both");
            }

            [$params, $call, $fallbackName] = $this->endpointCall($operation);
            $uses = array_merge(...array_map(fn (array $p): array => $p['type']->uses, $params));

            if ($typedJson) {
                [$returns, $returnDoc, $body, $returnUses, $bodyUses] = $this->jsonReturn($json, str_replace(', %ACCEPT%', '', $call));
                $methods[] = compact('name', 'summary', 'description', 'params', 'returns', 'returnDoc', 'body', 'bodyUses') + ['uses' => [...$uses, ...$returnUses]];
            } else {
                $accept = isset($ok['application/pdf']) ? "'application/pdf'" : "'*/*'";
                $methods[] = [
                    'name' => $name,
                    'summary' => $summary,
                    'description' => $description,
                    'params' => $params,
                    'returns' => self::FILE_RESPONSE,
                    'returnDoc' => $this->short(self::FILE_RESPONSE),
                    'uses' => [...$uses, self::FILE_RESPONSE],
                    'bodyUses' => [],
                    'body' => 'return '.$this->short(self::FILE_RESPONSE).'::fromResponse('.str_replace('%ACCEPT%', $accept, $call).($fallbackName === null ? '' : ", {$fallbackName}").');',
                ];
            }
        }

        return $methods;
    }

    /**
     * An endpoint that runs operations (create, edit, merge): it returns a PendingPdf
     * holding its other fields, which gets the operations and sends the request.
     *
     * @param  array<string, mixed>  $operation
     * @param  array<string, mixed>  $schema
     * @return array{name: string, summary: string, description: string, params: list<Field>, returns: string, returnDoc: string, uses: list<string>, bodyUses: list<string>, body: string}
     */
    private function builderMethod(array $operation, string $name, string $request, array $schema): array
    {
        $params = $this->fields($schema, preg_replace('/Request$/', '', $request) ?? $request, skip: ['operations', 'output']);
        $entries = array_map(fn (array $p): string => "    '{$p['name']}' => \${$p['name']},", $params);
        $pending = $this->short(self::PENDING_PDF);

        return [
            'name' => $name,
            'summary' => $operation['summary'] ?? '',
            'description' => ($operation['description'] ?? '')."\n\nChain {$pending}'s methods to add operations, then call store(), file() or download(), or return it from a route.",
            'params' => $params,
            'returns' => self::PENDING_PDF,
            'returnDoc' => $pending,
            'uses' => [...array_merge(...array_map(fn (array $p): array => $p['type']->uses, $params)), self::PENDING_PDF],
            'bodyUses' => [],
            'body' => "return new {$pending}(fn (array \$body, string \$accept) => \$this->post('{$operation['x-path']}', \$body, \$accept), [\n".implode("\n", $entries)."\n]);",
        ];
    }

    /**
     * The method's parameters and the Client call it makes; %ACCEPT% marks the Accept header.
     *
     * @param  array<string, mixed>  $operation
     * @return array{0: list<Field>, 1: string, 2: ?string}
     */
    private function endpointCall(array $operation): array
    {
        $path = $operation['x-path'];
        $request = Spec::refName($operation['requestBody']['content']['application/json']['schema'] ?? null);

        if ($request !== null) {
            $params = $this->fields($this->spec->component($request), preg_replace('/Request$/', '', $request) ?? $request);
            $entries = array_map(fn (array $p): string => "    '{$p['name']}' => \${$p['name']},", $params);

            return [$params, "\$this->post('{$path}', [\n".implode("\n", $entries)."\n], %ACCEPT%)", null];
        }

        $params = [];
        $url = "'{$path}'";
        $fallback = null;
        foreach ($operation['parameters'] ?? [] as $parameter) {
            if ($parameter['in'] !== 'path') {
                continue;
            }
            $params[] = ['name' => $parameter['name'], 'type' => PhpType::scalar('string', 'string'), 'required' => true, 'schema' => $parameter];
            $url = str_replace('{'.$parameter['name'].'}', "'.self::pathParam(\${$parameter['name']}).'", $url);
            $fallback = "basename(\${$parameter['name']})";
        }

        return [$params, '$this->get('.(preg_replace("/\\.''$/", '', $url) ?? $url).', %ACCEPT%)', $fallback];
    }

    /**
     * The return type, its PHPDoc, the method body, the classes the type names and the classes only the body names.
     *
     * @param  array<string, mixed>  $schema
     * @return array{0: string, 1: string, 2: string, 3: list<string>, 4: list<string>}
     */
    private function jsonReturn(array $schema, string $call): array
    {
        if (isset($schema['oneOf'])) {
            $members = array_values(array_map(fn (array $m): string => (string) Spec::refName($m), $schema['oneOf']));
            $classes = array_values(array_map(fn (string $m): string => $this->classes[$m], $members));

            return [implode('|', $classes), implode('|', array_map($this->short(...), $classes)), "\$data = {$call}->json();\n\n".$this->resolverBody($members, '$data'), $classes, []];
        }

        $name = (string) Spec::refName($schema);
        $class = $this->classes[$name];
        $type = $this->refType($name);

        return [$type->native, $type->doc, 'return '.$this->short($class)."::from({$call}->json());", $type->uses, [$class]];
    }

    /**
     * @param  list<array{name: string, summary: string, description: string, params: list<Field>, returns: string, returnDoc: string, uses: list<string>, bodyUses: list<string>, body: string}>  $methods
     */
    private function emitEndpoints(array $methods): void
    {
        $fqcn = self::NS.'\\Endpoints';
        [$file, $namespace] = $this->file($fqcn, [...array_merge(...array_column($methods, 'uses'), ...array_column($methods, 'bodyUses')), self::API_EXCEPTION]);
        $trait = $namespace->addTrait('Endpoints');
        $trait->addComment("The API's endpoints, one method each. Used by Client.");

        foreach ($methods as $spec) {
            $method = $trait->addMethod($spec['name'])->setReturnType($spec['returns']);
            $docs = [];
            foreach ($spec['params'] as $param) {
                $this->parameter($method, $param);
                $docs[] = $this->paramDoc($param);
            }
            $text = implode("\n\n", array_filter([$spec['summary'], $this->text($spec['description'])]));
            // A PendingPdf sends nothing until it is stored or returned, so only it can throw.
            $throws = $spec['returns'] === self::PENDING_PDF ? [] : ['', '@throws '.$this->short(self::API_EXCEPTION)];
            $method->addComment($text."\n\n".implode("\n", [...$docs, '@return '.$spec['returnDoc'], ...$throws]));
            $method->setBody($spec['body']);
        }

        $this->write($fqcn, $file);
    }

    /**
     * The facade, with an @method line per endpoint so IDEs and PHPStan know them.
     *
     * @param  list<array{name: string, summary: string, description: string, params: list<Field>, returns: string, returnDoc: string, uses: list<string>, bodyUses: list<string>, body: string}>  $methods
     */
    private function emitFacade(array $methods): void
    {
        $fqcn = self::NS.'\\Facades\\PdfMill';
        [$file, $namespace] = $this->file($fqcn, [...array_merge(...array_column($methods, 'uses')), 'Illuminate\\Support\\Facades\\Facade', self::NS.'\\Client']);
        $class = $namespace->addClass('PdfMill')->setFinal()->setExtends('Illuminate\\Support\\Facades\\Facade');

        $lines = ['The pdfmill API.', ''];
        foreach ($methods as $spec) {
            $params = array_map(function (array $p): string {
                $null = ! $p['required'] || $p['type']->nullable;

                return $p['type']->docOrNull($null)." \${$p['name']}".($p['required'] ? '' : ' = null');
            }, $spec['params']);
            $lines[] = "@method static {$spec['returnDoc']} {$spec['name']}(".implode(', ', $params).')';
        }
        $lines[] = '';
        $lines[] = '@see Client';
        $class->addComment(implode("\n", $lines));
        $class->addMethod('getFacadeAccessor')->setStatic()->setProtected()->setReturnType('string')->setBody('return Client::class;');

        $this->write($fqcn, $file);
    }

    // ---------- files and text ----------

    /**
     * @param  list<string>  $uses
     * @return array{0: PhpFile, 1: PhpNamespace}
     */
    private function file(string $fqcn, array $uses): array
    {
        $file = new PhpFile;
        $file->setStrictTypes();
        $file->addComment($this->header);
        $namespace = $file->addNamespace(substr($fqcn, 0, (int) strrpos($fqcn, '\\')));
        foreach (array_unique($uses) as $use) {
            $this->use($namespace, $use);
        }

        return [$file, $namespace];
    }

    private function use(PhpNamespace $namespace, string $fqcn): void
    {
        $classNamespace = str_contains($fqcn, '\\') ? substr($fqcn, 0, (int) strrpos($fqcn, '\\')) : '';
        if ($classNamespace !== $namespace->getName()) {
            $namespace->addUse($fqcn);
        }
    }

    private function write(string $fqcn, PhpFile $file): void
    {
        $relative = str_replace('\\', '/', substr($fqcn, strlen(self::NS) + 1)).'.php';
        if (isset($this->files[$relative])) {
            throw new RuntimeException("Two schemas would both generate {$relative}");
        }
        $this->files[$relative] = (new PsrPrinter)->printFile($file);
    }

    private function short(string $fqcn): string
    {
        $slash = strrpos($fqcn, '\\');

        return $slash === false ? $fqcn : substr($fqcn, $slash + 1);
    }

    /**
     * @param  array<string, mixed>  $schema
     */
    private function describe(ClassType $class, array $schema, ?string $extra = null): void
    {
        $text = trim(($schema['description'] ?? '')."\n\n".($extra ?? ''));
        if ($text !== '') {
            $class->addComment($this->text($text));
        }
    }

    /**
     * A field's description, with its default when the spec has one.
     *
     * @param  array<string, mixed>|bool  $schema
     */
    private function fieldText(array|bool $schema): string
    {
        if (! is_array($schema)) {
            return '';
        }
        $text = $schema['description'] ?? $this->spec->resolve($schema)['description'] ?? '';
        if (array_key_exists('default', $schema) && ! str_contains($text, 'Default')) {
            $default = json_encode($schema['default'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $text = rtrim($text, '. ').($text === '' ? '' : '. ').'Default: '.$default.'.';
        }

        return $this->text($text);
    }

    private function text(string $text): string
    {
        return str_replace('*/', '*\/', $text);
    }

    /**
     * Short class names must be unique: generated code refers to classes by short name.
     */
    private function assertUniqueShortNames(): void
    {
        $seen = [];
        foreach (array_keys($this->files) as $path) {
            $short = basename($path, '.php');
            if (isset($seen[$short])) {
                throw new RuntimeException("Two generated classes are named {$short}: {$seen[$short]} and {$path}");
            }
            $seen[$short] = $path;
        }
    }
}
