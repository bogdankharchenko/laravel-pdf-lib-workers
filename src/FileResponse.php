<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

/**
 * A file returned by the API: a PDF from PendingPdf::file(), or any stored
 * result from download(). Return it from a controller to show it in the
 * browser, or call download() or save().
 */
final readonly class FileResponse implements Responsable
{
    public function __construct(
        public string $contents,
        public string $contentType,
        public string $filename,
        /** Number of pages, for PDFs made by create, edit or merge. */
        public ?int $pageCount = null,
        /** R2 key, when the file was also stored (PendingPdf::file(storeAs: …)). */
        public ?string $key = null,
        /** Signed download link, when the file was also stored. */
        public ?string $url = null,
    ) {}

    public static function fromResponse(Response $response, string $fallbackFilename = 'document.pdf'): self
    {
        $pageCount = $response->header('X-Page-Count');

        return new self(
            contents: $response->body(),
            contentType: $response->header('Content-Type') ?: 'application/octet-stream',
            filename: self::filename($response->header('Content-Disposition')) ?? $fallbackFilename,
            pageCount: $pageCount === '' ? null : (int) $pageCount,
            key: $response->header('X-File-Key') ?: null,
            url: $response->header('X-File-Url') ?: null,
        );
    }

    public function size(): int
    {
        return strlen($this->contents);
    }

    /**
     * Saves the file to a Laravel filesystem disk and returns the path.
     */
    public function save(string $path, ?string $disk = null): string
    {
        Storage::disk($disk)->put($path, $this->contents);

        return $path;
    }

    /**
     * A response that makes the browser save the file.
     */
    public function download(?string $filename = null): HttpResponse
    {
        return $this->respond(HeaderUtils::DISPOSITION_ATTACHMENT, $filename ?? $this->filename);
    }

    /**
     * A response that shows the file in the browser.
     */
    public function toResponse($request): HttpResponse
    {
        return $this->respond(HeaderUtils::DISPOSITION_INLINE, $this->filename);
    }

    private function respond(string $disposition, string $filename): HttpResponse
    {
        return new HttpResponse($this->contents, 200, [
            'Content-Type' => $this->contentType,
            'Content-Length' => (string) $this->size(),
            'Content-Disposition' => HeaderUtils::makeDisposition($disposition, $filename, self::ascii($filename)),
        ]);
    }

    /**
     * The name in a Content-Disposition header: filename* (RFC 6266, for
     * non-ASCII names) when given, else filename. Paths are cut to the base name.
     */
    private static function filename(string $disposition): ?string
    {
        $params = HeaderUtils::combine(HeaderUtils::split($disposition, ';='));
        $extended = $params['filename*'] ?? null;
        $name = is_string($extended) && preg_match("/^UTF-8'[^']*'(.+)$/i", $extended, $match) === 1
            ? rawurldecode($match[1])
            : $params['filename'] ?? null;

        return is_string($name) && $name !== '' ? basename($name) : null;
    }

    /**
     * The name with one "_" for each character a plain filename="…" can't hold.
     */
    private static function ascii(string $filename): string
    {
        return (string) preg_replace('/[^\x20-\x7e]|[%\/\\\\]/u', '_', mb_scrub($filename, 'UTF-8'));
    }
}
