<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Testing;

use BogdanKharchenko\PdfMill\Data\StoredPdf;
use BogdanKharchenko\PdfMill\Exceptions\ApiException;
use BogdanKharchenko\PdfMill\FileResponse;
use BogdanKharchenko\PdfMill\Support\Encoder;
use Closure;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use InvalidArgumentException;
use LogicException;
use Psr\Http\Message\StreamInterface;
use Spatie\LaravelData\Data;
use Throwable;

/**
 * The other end of PdfMillFake's network: answers each request with the reply
 * set for its endpoint or as the API would, and keeps it for assertions.
 * (Not part of PdfMillFake itself, whose methods share a namespace with the
 * generated endpoint methods.)
 *
 * @internal
 *
 * @phpstan-import-type Reply from PdfMillFake
 * @phpstan-import-type Replies from PdfMillFake
 */
class FakeServer
{
    public const URL = 'https://pdfmill.test';

    /** Endpoints that make a PDF, returning a PendingPdf. */
    private const BUILDERS = ['create', 'edit', 'merge'];

    /** @var list<SentRequest> */
    private array $sent = [];

    /**
     * @param  array<string, string>  $routes  Each endpoint's method and path, by name: Client::ROUTES.
     * @param  Replies  $replies
     */
    public function __construct(
        private readonly array $routes,
        private readonly array $replies,
    ) {
        foreach ($replies as $endpoint => $reply) {
            $this->ensureEndpoint($endpoint);
            if (! $reply instanceof Closure) {
                self::ensureReply($endpoint, $reply);
            }
        }
    }

    /**
     * Records the request, then replies as set or as the API would.
     */
    public function answer(Request $request): PromiseInterface
    {
        $this->sent[] = $sent = $this->record($request);
        $reply = $this->replies[$sent->endpoint] ?? null;
        if ($reply instanceof Closure) {
            $reply = $reply($sent);
            self::ensureReply($sent->endpoint, $reply);
        }

        return match (true) {
            $reply instanceof PromiseInterface => $reply,
            $reply instanceof ApiException => self::json((new Encoder)->decoded($reply->error), $reply->status),
            $reply instanceof Throwable => Create::rejectionFor($reply),
            in_array($sent->endpoint, self::BUILDERS, true) => self::pdf($sent, $request->hasHeader('Accept', 'application/pdf'), $reply),
            $sent->endpoint === 'download' => self::file($reply instanceof FileResponse ? $reply : new FileResponse(self::blankPdf(), 'application/pdf', basename($sent->data['key']))),
            $reply instanceof Data => self::json((new Encoder)->decoded($reply)),
            default => self::json(self::merge(self::defaultJson($sent), is_array($reply) ? $reply : [])),
        };
    }

    /**
     * The requests answered, in order: all, to one endpoint, or those the callback accepts.
     *
     * @param  string|(Closure(SentRequest): bool)|null  $endpoint
     * @param  (Closure(SentRequest): bool)|null  $callback
     * @return Collection<int, SentRequest>
     */
    public function sent(string|Closure|null $endpoint = null, ?Closure $callback = null): Collection
    {
        if ($endpoint instanceof Closure) {
            [$endpoint, $callback] = [null, $endpoint];
        } elseif ($endpoint !== null) {
            $this->ensureEndpoint($endpoint);
        }

        return (new Collection($this->sent))
            ->filter(fn (SentRequest $request): bool => ($endpoint === null || $request->endpoint === $endpoint) && ($callback === null || $callback($request)))
            ->values();
    }

    /**
     * What was sent, for failure messages: "Sent: merge, info." or "Nothing was sent."
     */
    public function summary(): string
    {
        return $this->sent === [] ? 'Nothing was sent.' : 'Sent: '.implode(', ', array_column($this->sent, 'endpoint')).'.';
    }

    /**
     * @throws InvalidArgumentException for a name that isn't an endpoint, such as a typo.
     */
    private function ensureEndpoint(string $endpoint): void
    {
        if (! array_key_exists($endpoint, $this->routes)) {
            throw new InvalidArgumentException("pdfmill has no endpoint [{$endpoint}]; it has ".implode(', ', array_keys($this->routes)).'.');
        }
    }

    /**
     * @throws InvalidArgumentException for a reply the endpoint can't give, such as a FileResponse from info.
     */
    private static function ensureReply(string $endpoint, mixed $reply): void
    {
        $expected = match (true) {
            $reply === null, $reply instanceof PromiseInterface, $reply instanceof Throwable => null,
            in_array($endpoint, self::BUILDERS, true) => is_array($reply) || $reply instanceof StoredPdf || $reply instanceof FileResponse
                ? null
                : 'a StoredPdf, a FileResponse or an array of StoredPdf fields',
            $endpoint === 'download' => $reply instanceof FileResponse ? null : 'a FileResponse',
            default => is_array($reply) || $reply instanceof Data ? null : 'its result object or an array of fields',
        };

        if ($expected !== null) {
            throw new InvalidArgumentException("PdfMill::fake(): {$endpoint} replies with {$expected}, not ".get_debug_type($reply).'.');
        }
    }

    /**
     * The request as the API would read it: its endpoint, options and files.
     */
    private function record(Request $request): SentRequest
    {
        [$endpoint, $params] = $this->route($request);

        if (! $request->isMultipart()) {
            return new SentRequest($endpoint, [...$params, ...$request->data()]);
        }

        $data = [];
        $files = [];
        foreach ($request->data() as $part) {
            $contents = self::contents($part['contents']);
            if ($part['name'] === 'options') {
                $data = (array) json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
            } else {
                $files[$part['name']] = ['filename' => $part['filename'] ?? null, 'contents' => $contents];
            }
        }

        return new SentRequest($endpoint, $data, $files);
    }

    /**
     * The endpoint a request is for, and the parameters in its path, such as download's key.
     *
     * @return array{string, array<string, string>}
     */
    private function route(Request $request): array
    {
        $path = (string) parse_url($request->url(), PHP_URL_PATH);

        foreach ($this->routes as $endpoint => $route) {
            [$method, $template] = explode(' ', $route, 2);
            $pattern = preg_replace_callback('/\{(\w+)\}|[^{]+/', fn (array $part): string => isset($part[1]) ? "(?<{$part[1]}>.+)" : preg_quote($part[0], '#'), $template);

            if ($request->method() === $method && preg_match("#^{$pattern}$#", $path, $match) === 1) {
                return [$endpoint, array_map(rawurldecode(...), array_filter($match, is_string(...), ARRAY_FILTER_USE_KEY))];
            }
        }

        throw new LogicException("No pdfmill endpoint answers {$request->method()} {$path}.");
    }

    /**
     * The PDF that create, edit or merge made: its StoredPdf, or the file itself.
     *
     * @param  Reply|null  $reply
     */
    private static function pdf(SentRequest $sent, bool $wantsFile, mixed $reply): PromiseInterface
    {
        $output = $sent->data['output'] ?? [];
        $file = $reply instanceof FileResponse ? $reply : null;
        $key = $file->key ?? $output['key'] ?? 'outputs/'.Str::uuid().'.pdf';
        $default = [
            'key' => $key,
            ...self::link($key, $output['linkTtl'] ?? null),
            'size' => strlen($file->contents ?? self::blankPdf()),
            'pageCount' => $file->pageCount ?? 1,
        ];

        $stored = match (true) {
            $reply instanceof StoredPdf => (new Encoder)->decoded($reply),
            $reply instanceof FileResponse => self::merge($default, array_filter(['url' => $reply->url])),
            default => self::merge($default, is_array($reply) ? $reply : []),
        };

        if (! $wantsFile) {
            return self::json($stored);
        }

        $kept = ($output['store'] ?? true) !== false;

        return self::file(new FileResponse(
            contents: $file->contents ?? self::blankPdf(),
            contentType: $file->contentType ?? 'application/pdf',
            filename: $file->filename ?? $output['filename'] ?? 'document.pdf',
            pageCount: $stored['pageCount'],
            key: $kept ? $stored['key'] : null,
            url: $kept ? $stored['url'] : null,
        ));
    }

    /**
     * What the API answers for a one-page blank A4 PDF.
     *
     * @return array<string, mixed>
     */
    private static function defaultJson(SentRequest $sent): array
    {
        $data = $sent->data;

        return match ($sent->endpoint) {
            'info' => [
                'pageCount' => 1,
                'encrypted' => false,
                'pdfA' => null,
                'metadata' => array_fill_keys(['title', 'author', 'subject', 'keywords', 'creator', 'producer', 'language', 'creationDate', 'modificationDate', 'copyright', 'copyrightUrl'], null) + ['custom' => []],
                'pages' => [[
                    'page' => 1,
                    'width' => 595.28,
                    'height' => 841.89,
                    'rotation' => 0,
                    'boxes' => array_fill_keys(['mediaBox', 'cropBox', 'bleedBox', 'trimBox', 'artBox'], ['x' => 0, 'y' => 0, 'width' => 595.28, 'height' => 841.89]),
                ]],
                'form' => ['hasXFA' => false, 'fields' => [], 'signatureFields' => []],
                'layers' => [],
                'viewerPreferences' => ['pageMode' => null, 'pageLayout' => null],
                'attachments' => [],
                'hasJavaScript' => false,
            ],
            'text' => ['pages' => [['page' => 1, 'text' => '', ...(($data['items'] ?? false) ? ['items' => []] : [])]]],
            'extract' => self::extracted($data['include'] ?? ['images', 'attachments']),
            'scripts' => ['document' => [], 'fields' => [], 'pages' => [], 'xfa' => []],
            'split' => self::parts($data['prefix'] ?? 'outputs/'.Str::uuid().'/', max(1, count($data['ranges'] ?? [])), $data['linkTtl'] ?? null),
            'measureText' => self::measured($data['text'], $data['size'] ?? 12, $data['lineHeight'] ?? null, $data['fitHeight'] ?? null),
            default => throw new LogicException("PdfMill::fake() has no default reply for {$sent->endpoint}: give it one."),
        };
    }

    /**
     * @param  list<string>  $include
     * @return array<string, mixed>
     */
    private static function extracted(array $include): array
    {
        $page = array_intersect_key(['page' => 1, 'text' => '', 'images' => [], 'graphics' => []], array_flip(['page', ...$include]));

        return ['pages' => [$page], ...(in_array('attachments', $include, true) ? ['attachments' => []] : [])];
    }

    /**
     * Split's parts: one per range, or one in all, since the blank PDF has one page.
     *
     * @return array<string, mixed>
     */
    private static function parts(string $prefix, int $count, ?int $linkTtl): array
    {
        return ['parts' => array_map(function (int $part) use ($prefix, $linkTtl): array {
            $key = $prefix.sprintf('part-%03d.pdf', $part);

            return ['key' => $key, ...self::link($key, $linkTtl), 'pages' => [$part], 'size' => strlen(self::blankPdf())];
        }, range(1, $count))];
    }

    /**
     * Each character is half the font size wide, and a line as tall as the size.
     *
     * @return array<string, mixed>
     */
    private static function measured(string $text, int|float $size, int|float|null $lineHeight, int|float|null $fitHeight): array
    {
        $lines = array_map(fn (string $line): array => ['text' => $line, 'width' => mb_strlen($line) * $size / 2], explode("\n", $text));

        return [
            'width' => max(array_column($lines, 'width')),
            'height' => $size,
            'ascent' => $size * 0.75,
            'lines' => $lines,
            'blockHeight' => $size + (count($lines) - 1) * ($lineHeight ?? $size * 1.2),
            'sizeForHeight' => $fitHeight,
        ];
    }

    /**
     * A signed download link to a stored file, as the API would make it.
     *
     * @return array{url: string, expiresAt: string}
     */
    private static function link(string $key, ?int $ttl): array
    {
        $expires = Date::now()->addSeconds($ttl ?? 3600)->utc();
        $path = implode('/', array_map(rawurlencode(...), explode('/', $key)));

        return [
            'url' => self::URL."/files/{$path}?expires={$expires->getTimestamp()}&sig=fake",
            'expiresAt' => $expires->format('Y-m-d\TH:i:s.v\Z'),
        ];
    }

    /**
     * A part's contents: Laravel 12 records an attached stream as the resource it was given.
     */
    private static function contents(mixed $contents): string
    {
        if ($contents instanceof StreamInterface) {
            $contents->rewind();

            return $contents->getContents();
        }

        if (is_resource($contents)) {
            rewind($contents);

            return (string) stream_get_contents($contents);
        }

        return (string) $contents;
    }

    /**
     * @param  array<array-key, mixed>  $body
     */
    private static function json(array $body, int $status = 200): PromiseInterface
    {
        return Factory::response(json_encode($body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $status, ['Content-Type' => 'application/json']);
    }

    private static function file(FileResponse $file): PromiseInterface
    {
        return Factory::response($file->contents, 200, array_filter([
            'Content-Type' => $file->contentType,
            'Content-Disposition' => "inline; filename*=UTF-8''".rawurlencode($file->filename),
            'X-Page-Count' => $file->pageCount === null ? null : (string) $file->pageCount,
            'X-File-Key' => $file->key,
            'X-File-Url' => $file->url,
        ], fn (?string $value): bool => $value !== null));
    }

    /**
     * $default with $changes: objects merged field by field; anything else, lists included, replaced.
     *
     * @param  array<array-key, mixed>  $default
     * @param  array<array-key, mixed>  $changes
     * @return array<array-key, mixed>
     */
    private static function merge(array $default, array $changes): array
    {
        foreach ($changes as $key => $value) {
            $default[$key] = is_array($value) && ! array_is_list($value) && is_array($default[$key] ?? null) && ! array_is_list($default[$key])
                ? self::merge($default[$key], $value)
                : $value;
        }

        return $default;
    }

    /**
     * A valid one-page A4 PDF with nothing on it.
     */
    private static function blankPdf(): string
    {
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595.28 841.89] /Resources << >> >>',
        ];
        $pdf = "%PDF-1.7\n";
        $xref = "xref\n0 4\n0000000000 65535 f \n";
        foreach ($objects as $number => $object) {
            $xref .= sprintf("%010d 00000 n \n", strlen($pdf));
            $pdf .= ($number + 1)." 0 obj\n{$object}\nendobj\n";
        }

        return $pdf.$xref."trailer\n<< /Size 4 /Root 1 0 R >>\nstartxref\n".strlen($pdf)."\n%%EOF\n";
    }
}
