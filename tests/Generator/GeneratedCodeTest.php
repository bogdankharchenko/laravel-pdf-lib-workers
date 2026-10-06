<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Tests\Generator;

use BogdanKharchenko\PdfMill\Client;
use BogdanKharchenko\PdfMill\Endpoints;
use BogdanKharchenko\PdfMill\Facades\PdfMill;
use BogdanKharchenko\PdfMill\Generator\Generator;
use BogdanKharchenko\PdfMill\Generator\Spec;
use BogdanKharchenko\PdfMill\Testing\PdfMillFake;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionClassConstant;
use ReflectionMethod;
use SplFileInfo;

class GeneratedCodeTest extends TestCase
{
    private const ROOT = __DIR__.'/../..';

    public function test_generated_code_is_up_to_date_with_openapi_json(): void
    {
        $spec = self::ROOT.'/openapi.json';
        $expected = (new Generator(Spec::load($spec), (string) hash_file('sha256', $spec)))->generate();
        $actual = array_map(fn (string $path): string => (string) file_get_contents($path), $this->phpFiles('generated'));
        ksort($expected);
        ksort($actual);

        $this->assertSame(array_keys($expected), array_keys($actual), 'Files differ: run composer generate');
        foreach ($expected as $path => $source) {
            $this->assertSame($source, $actual[$path], "generated/{$path} is out of date: run composer generate");
        }
    }

    public function test_every_import_is_used(): void
    {
        foreach ([...array_values($this->phpFiles('src')), ...array_values($this->phpFiles('generated'))] as $path) {
            $code = (string) file_get_contents($path);
            preg_match_all('/^use (?:function |const )?([\w\\\\]+)(?: as (\w+))?;$/m', $code, $imports, PREG_SET_ORDER);
            $rest = (string) preg_replace('/^use [^;]+;$/m', '', $code);

            foreach ($imports as $import) {
                $name = $import[2] ?? substr((string) strrchr('\\'.$import[1], '\\'), 1);
                $this->assertMatchesRegularExpression('/\b'.$name.'\b/', $rest, "{$path} imports {$import[1]} but doesn't use it");
            }
        }
    }

    /**
     * PdfMillFake is a Client and the facade forwards to it, so an endpoint
     * method named like one of their methods would break all three.
     */
    public function test_reserves_the_method_names_of_the_client_the_fake_and_the_facade(): void
    {
        $reserved = (new ReflectionClassConstant(Generator::class, 'RESERVED_METHODS'))->getValue();
        $this->assertIsArray($reserved);
        $endpoints = array_map(fn (ReflectionMethod $method): string => $method->name, (new ReflectionClass(Endpoints::class))->getMethods());

        $own = [];
        foreach ([Client::class, PdfMillFake::class, PdfMill::class] as $class) {
            foreach ((new ReflectionClass($class))->getMethods() as $method) {
                if ($method->class === $class && ! $method->isConstructor() && ! in_array($method->name, $endpoints, true)) {
                    $own[] = $method->name;
                }
            }
        }

        $this->assertSame([], array_values(array_diff($own, ['getFacadeAccessor'], $reserved)), 'Add these to Generator::RESERVED_METHODS');
    }

    /**
     * @return array<string, string> path relative to the directory => absolute path
     */
    private function phpFiles(string $directory): array
    {
        $root = realpath(self::ROOT.'/'.$directory);
        $this->assertIsString($root);

        $files = [];
        /** @var SplFileInfo $file */
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
            if ($file->getExtension() === 'php') {
                $files[substr($file->getPathname(), strlen($root) + 1)] = $file->getPathname();
            }
        }

        return $files;
    }
}
