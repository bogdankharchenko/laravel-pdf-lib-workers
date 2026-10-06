<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Testing;

use BogdanKharchenko\PdfMill\Client;
use BogdanKharchenko\PdfMill\FileResponse;
use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Testing\Fakes\Fake;
use PHPUnit\Framework\Assert as PHPUnit;
use Spatie\LaravelData\Data;
use Throwable;

/**
 * Stands in for the API in tests; PdfMill::fake() swaps it in. Nothing leaves
 * the app: each request is recorded and answered as the API would answer it.
 * It is a Client, so options are encoded and files read just as in production,
 * and a Client injected into your code is faked too.
 *
 * Each endpoint answers as if every PDF were one blank A4 page:
 * - store() returns a StoredPdf under your key, or "outputs/<uuid>.pdf";
 * - put() returns an UploadedPdf but uploads nothing: if your code reads the
 *   file back, write it there from a closure reply;
 * - file(), download() and returning a PendingPdf give that page as a PDF;
 * - info, text, extract, scripts and split describe it; measureText makes
 *   each character half the font size wide and each line as tall as the size.
 * Freeze UUIDs (Str::freezeUuids()) and time ($this->freezeTime()) to know
 * keys and expiry times in advance.
 *
 * Change an endpoint's reply by its name:
 * - an array: fields to change in the default reply, e.g. ['pageCount' => 3];
 * - a result object: the whole reply, e.g. an InfoResponse. create, edit and
 *   merge take a StoredPdf, an UploadedPdf or a FileResponse, download a
 *   FileResponse;
 * - an ApiException: the API answers with that error, so your code gets it;
 * - any other exception, e.g. a ConnectionException: thrown, as if sending failed;
 * - Http::response(): exactly that HTTP reply;
 * - a closure that gets the SentRequest and returns one of these, or null
 *   for the default.
 *
 * @phpstan-type Reply array<string, mixed>|Data|FileResponse|Throwable|PromiseInterface
 * @phpstan-type Replies array<string, Reply|Closure(SentRequest): (Reply|null)>
 */
class PdfMillFake extends Client implements Fake
{
    private readonly FakeServer $server;

    /**
     * @param  Replies  $replies  Replies by endpoint name.
     */
    public function __construct(array $replies = [])
    {
        $this->server = new FakeServer(self::ROUTES, $replies);

        $http = new Factory;
        $http->fake($this->server->answer(...))->preventStrayRequests();

        parent::__construct($http, FakeServer::URL, 'fake-key');
    }

    /**
     * Asserts a request was sent: to an endpoint, an exact number of times,
     * or one the callback accepts.
     *
     *   PdfMill::assertSent('merge');
     *   PdfMill::assertSent('edit', 2);
     *   PdfMill::assertSent('edit', fn (SentRequest $pdf) => $pdf->hasOperation('flattenForm'));
     *   PdfMill::assertSent(fn (SentRequest $request) => $request->data['source'] === 'in.pdf');
     *
     * @param  string|(Closure(SentRequest): bool)  $endpoint
     * @param  (Closure(SentRequest): bool)|int|null  $callback  A callback, or how many times.
     */
    public function assertSent(string|Closure $endpoint, Closure|int|null $callback = null): void
    {
        $times = is_int($callback) ? $callback : null;
        $callback = is_int($callback) ? null : $callback;
        $count = $this->server->sent($endpoint, $callback)->count();

        if ($times === null) {
            PHPUnit::assertTrue($count > 0, 'No '.self::describe($endpoint, $callback).' was sent. '.$this->server->summary());
        } else {
            PHPUnit::assertSame($times, $count, "Expected {$times} ".self::describe($endpoint, $callback, $times).", got {$count}. ".$this->server->summary());
        }
    }

    /**
     * Asserts no request was sent to the endpoint, or none the callback accepts.
     *
     * @param  string|(Closure(SentRequest): bool)  $endpoint
     * @param  (Closure(SentRequest): bool)|null  $callback
     */
    public function assertNotSent(string|Closure $endpoint, ?Closure $callback = null): void
    {
        PHPUnit::assertTrue(
            $this->server->sent($endpoint, $callback)->isEmpty(),
            'Unexpected '.self::describe($endpoint, $callback).'. '.$this->server->summary(),
        );
    }

    public function assertSentCount(int $count): void
    {
        $sent = $this->server->sent()->count();

        PHPUnit::assertSame($count, $sent, "Expected {$count} ".Str::plural('request', $count).", got {$sent}. ".$this->server->summary());
    }

    public function assertNothingSent(): void
    {
        PHPUnit::assertTrue($this->server->sent()->isEmpty(), 'Expected no requests. '.$this->server->summary());
    }

    /**
     * The requests sent, in order: all, to one endpoint, or those the callback accepts.
     *
     * @param  string|(Closure(SentRequest): bool)|null  $endpoint
     * @param  (Closure(SentRequest): bool)|null  $callback
     * @return Collection<int, SentRequest>
     */
    public function sent(string|Closure|null $endpoint = null, ?Closure $callback = null): Collection
    {
        return $this->server->sent($endpoint, $callback);
    }

    /**
     * E.g. "edit request matching the callback", for failure messages.
     */
    private static function describe(string|Closure $endpoint, ?Closure $callback, int $count = 1): string
    {
        $matching = $endpoint instanceof Closure || $callback !== null ? ' matching the callback' : '';

        return (is_string($endpoint) ? "{$endpoint} " : '').Str::plural('request', $count).$matching;
    }
}
