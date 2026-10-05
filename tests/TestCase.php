<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Tests;

use BogdanKharchenko\PdfLibWorkers\PdfLibWorkersServiceProvider;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Orchestra\Testbench\TestCase as Orchestra;
use Psr\Http\Message\StreamInterface;
use Spatie\LaravelData\LaravelDataServiceProvider;
use Spatie\LaravelData\Support\DataContainer;

abstract class TestCase extends Orchestra
{
    private const JSON_FLAGS = JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

    protected function setUp(): void
    {
        parent::setUp();

        // laravel-data keeps its resolvers, and the config they read, in a static
        // container; without a reset, one test's config would leak into the next.
        DataContainer::get()->reset();
        Http::preventStrayRequests();
    }

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
     * Answers each endpoint the request tests call with a captured response.
     */
    protected function fakeEndpoints(): void
    {
        Http::fake([
            'pdf.test/pdf/info' => Http::response($this->fixture('info')['body']),
            'pdf.test/pdf/extract' => Http::response($this->fixture('extract-stored')['body']),
            '*' => Http::response($this->fixture('create-stored')['body']),
        ]);
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
     * The JSON body sent is exactly $expected: same fields, order and types,
     * and {} stays distinct from [].
     */
    protected function assertSentJson(string $expected): void
    {
        $request = $this->sentRequest();
        $this->assertSame('application/json', $request->header('Content-Type')[0] ?? null);
        $this->assertJsonIs($expected, $request->body());
    }

    protected function assertJsonIs(string $expected, string $actual): void
    {
        $this->assertSame(
            json_encode(json_decode($expected, flags: JSON_THROW_ON_ERROR), self::JSON_FLAGS),
            json_encode(json_decode($actual, flags: JSON_THROW_ON_ERROR), self::JSON_FLAGS),
        );
    }

    /**
     * The parts of the multipart request sent, by name, with their contents read.
     *
     * @return array<string, array{contents: string, filename: ?string}>
     */
    protected function sentParts(): array
    {
        $request = $this->sentRequest();
        $this->assertTrue($request->isMultipart(), 'Expected a multipart request');

        $parts = [];
        foreach ($request->data() as $part) {
            $contents = $part['contents'];
            if ($contents instanceof StreamInterface) {
                $contents->rewind();
                $contents = $contents->getContents();
            }
            $parts[$part['name']] = ['contents' => (string) $contents, 'filename' => $part['filename'] ?? null];
        }

        return $parts;
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
