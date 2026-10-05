<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill;

use BogdanKharchenko\PdfMill\Contracts\Payload;
use BogdanKharchenko\PdfMill\Support\Fields;
use BogdanKharchenko\PdfMill\Support\JsonMap;
use SplFileInfo;

/**
 * A TTF, OTF, TTC or DFONT font file: exactly one of key, url, base64 or upload.
 *
 * Create one with FontSource::key(), FontSource::url(), FontSource::base64(), FontSource::file(), ::contents(), ::disk().
 */
final readonly class FontSource implements Payload
{
    /**
     * @param  array<string, mixed>  $fields
     */
    private function __construct(
        private array $fields,
    ) {
    }

    /**
     * @param  string  $key  Object key in the R2 bucket, e.g. a template or a previous result.
     * @param  string|null  $postscriptName  Picks one face from a .ttc/.dfont collection, e.g. "Helvetica-Bold".
     */
    public static function key(string $key, ?string $postscriptName = null): self
    {
        return new self(Fields::compact([
            'key' => $key,
            'postscriptName' => $postscriptName,
        ]));
    }

    /**
     * @param  string  $url  http(s) URL the Worker downloads. Some sites block requests from Cloudflare Workers; send those files as uploads instead.
     * @param  array<array-key, string>|null  $headers  Extra request headers for `url`, e.g. { "authorization": "Bearer …" } for private files.
     * @param  string|null  $postscriptName  Picks one face from a .ttc/.dfont collection, e.g. "Helvetica-Bold".
     */
    public static function url(string $url, ?array $headers = null, ?string $postscriptName = null): self
    {
        return new self(Fields::compact([
            'url' => $url,
            'headers' => is_array($headers) ? new JsonMap($headers) : $headers,
            'postscriptName' => $postscriptName,
        ]));
    }

    /**
     * @param  string  $base64  The file's bytes as base64, or a data: URL.
     * @param  string|null  $postscriptName  Picks one face from a .ttc/.dfont collection, e.g. "Helvetica-Bold".
     */
    public static function base64(string $base64, ?string $postscriptName = null): self
    {
        return new self(Fields::compact([
            'base64' => $base64,
            'postscriptName' => $postscriptName,
        ]));
    }

    /**
     * A file on disk, or one uploaded to your app ($request->file('…')). It is sent with the request.
     *
     * @param  string|null  $filename  Name sent with the file. Default: the uploaded or base name.
     * @param  string|null  $postscriptName  Picks one face from a .ttc/.dfont collection, e.g. "Helvetica-Bold".
     */
    public static function file(
        SplFileInfo|string $file,
        string|null $filename = null,
        ?string $postscriptName = null,
    ): self {
        return new self(Fields::compact([
            'upload' => Upload::fromFile($file, $filename),
            'postscriptName' => $postscriptName,
        ]));
    }

    /**
     * Bytes you already have in memory. They are sent with the request.
     *
     * @param  string|null  $postscriptName  Picks one face from a .ttc/.dfont collection, e.g. "Helvetica-Bold".
     */
    public static function contents(string $contents, string $filename, ?string $postscriptName = null): self
    {
        return new self(Fields::compact([
            'upload' => Upload::fromContents($contents, $filename),
            'postscriptName' => $postscriptName,
        ]));
    }

    /**
     * A file on one of your Laravel filesystem disks. It is sent with the request.
     *
     * @param  string|null  $disk  Default: your default disk.
     * @param  string|null  $postscriptName  Picks one face from a .ttc/.dfont collection, e.g. "Helvetica-Bold".
     */
    public static function disk(string $path, string|null $disk = null, ?string $postscriptName = null): self
    {
        return new self(Fields::compact([
            'upload' => Upload::fromDisk($path, $disk),
            'postscriptName' => $postscriptName,
        ]));
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return $this->fields;
    }
}
