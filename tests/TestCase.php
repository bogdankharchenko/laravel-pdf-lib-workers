<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Tests;

use BogdanKharchenko\PdfLibWorkers\PdfLibWorkersServiceProvider;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelData\LaravelDataServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [LaravelDataServiceProvider::class, PdfLibWorkersServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('pdf-lib-workers.url', 'https://pdf.test');
        $app['config']->set('pdf-lib-workers.key', 'test-key');
    }

    /**
     * A response captured from the real API (see tests/fixtures).
     *
     * @return array{status: int, body: array<string, mixed>}
     */
    protected function fixture(string $name): array
    {
        $json = file_get_contents(__DIR__."/fixtures/{$name}.json");
        $this->assertIsString($json, "Missing fixture {$name}");

        return json_decode($json, true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * Answers every request with a captured response.
     */
    protected function respondWith(string $fixture): void
    {
        ['status' => $status, 'body' => $body] = $this->fixture($fixture);
        Http::fake(['*' => Http::response($body, $status)]);
    }

    /**
     * The one request the test sent.
     */
    protected function sentRequest(): Request
    {
        $recorded = Http::recorded();
        $this->assertCount(1, $recorded, 'Expected exactly one request');

        return $recorded[0][0];
    }

    /**
     * @return array<string, mixed>
     */
    protected function sentJson(): array
    {
        return json_decode($this->sentRequest()->body(), true, flags: JSON_THROW_ON_ERROR);
    }

    /**
     * Every field of $expected is in $actual with an equal value, recursively.
     * Catches response fields the generated classes don't know about.
     *
     * @param  array<array-key, mixed>  $expected
     * @param  array<array-key, mixed>  $actual
     */
    protected function assertContainsJson(array $expected, array $actual, string $path = '$'): void
    {
        foreach ($expected as $key => $value) {
            $this->assertArrayHasKey($key, $actual, "{$path}.{$key} is missing: the class doesn't read this field");
            is_array($value) && is_array($actual[$key])
                ? $this->assertContainsJson($value, $actual[$key], "{$path}.{$key}")
                : $this->assertEquals($value, $actual[$key], "{$path}.{$key} differs");
        }
    }
}
