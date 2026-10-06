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
 * A PDF: exactly one of key, url, base64 or upload, plus options for opening it.
 *
 * Create one with PdfSource::key(), PdfSource::url(), PdfSource::base64(), PdfSource::file(), ::contents(), ::disk().
 */
readonly class PdfSource implements Payload
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
     * @param  string|null  $password  Password for an encrypted PDF. The result is saved without a password unless you add an `encrypt` operation.
     * @param  bool|null  $preserveXFA  Keep XFA form data (Adobe dynamic forms). Without it, operations that touch the form remove XFA.
     */
    public static function key(string $key, ?string $password = null, ?bool $preserveXFA = null): self
    {
        return new self(Fields::compact([
            'key' => $key,
            'password' => $password,
            'preserveXFA' => $preserveXFA,
        ]));
    }

    /**
     * @param  string  $url  http(s) URL the Worker downloads. Some sites block requests from Cloudflare Workers; send those files as uploads instead.
     * @param  array<array-key, string>|null  $headers  Extra request headers for `url`, e.g. { "authorization": "Bearer …" } for private files.
     * @param  string|null  $password  Password for an encrypted PDF. The result is saved without a password unless you add an `encrypt` operation.
     * @param  bool|null  $preserveXFA  Keep XFA form data (Adobe dynamic forms). Without it, operations that touch the form remove XFA.
     */
    public static function url(
        string $url,
        ?array $headers = null,
        ?string $password = null,
        ?bool $preserveXFA = null,
    ): self {
        return new self(Fields::compact([
            'url' => $url,
            'headers' => is_array($headers) ? new JsonMap($headers) : $headers,
            'password' => $password,
            'preserveXFA' => $preserveXFA,
        ]));
    }

    /**
     * @param  string  $base64  The file's bytes as base64, or a data: URL.
     * @param  string|null  $password  Password for an encrypted PDF. The result is saved without a password unless you add an `encrypt` operation.
     * @param  bool|null  $preserveXFA  Keep XFA form data (Adobe dynamic forms). Without it, operations that touch the form remove XFA.
     */
    public static function base64(string $base64, ?string $password = null, ?bool $preserveXFA = null): self
    {
        return new self(Fields::compact([
            'base64' => $base64,
            'password' => $password,
            'preserveXFA' => $preserveXFA,
        ]));
    }

    /**
     * A file on disk, or one uploaded to your app ($request->file('…')). It is sent with the request.
     *
     * @param  string|null  $filename  Name sent with the file. Default: the uploaded or base name.
     * @param  string|null  $password  Password for an encrypted PDF. The result is saved without a password unless you add an `encrypt` operation.
     * @param  bool|null  $preserveXFA  Keep XFA form data (Adobe dynamic forms). Without it, operations that touch the form remove XFA.
     */
    public static function file(
        SplFileInfo|string $file,
        string|null $filename = null,
        ?string $password = null,
        ?bool $preserveXFA = null,
    ): self {
        return new self(Fields::compact([
            'upload' => Upload::fromFile($file, $filename),
            'password' => $password,
            'preserveXFA' => $preserveXFA,
        ]));
    }

    /**
     * Bytes you already have in memory. They are sent with the request.
     *
     * @param  string|null  $password  Password for an encrypted PDF. The result is saved without a password unless you add an `encrypt` operation.
     * @param  bool|null  $preserveXFA  Keep XFA form data (Adobe dynamic forms). Without it, operations that touch the form remove XFA.
     */
    public static function contents(
        string $contents,
        string $filename,
        ?string $password = null,
        ?bool $preserveXFA = null,
    ): self {
        return new self(Fields::compact([
            'upload' => Upload::fromContents($contents, $filename),
            'password' => $password,
            'preserveXFA' => $preserveXFA,
        ]));
    }

    /**
     * A file on one of your Laravel filesystem disks. It is sent with the request.
     *
     * @param  string|null  $disk  Default: your default disk.
     * @param  string|null  $password  Password for an encrypted PDF. The result is saved without a password unless you add an `encrypt` operation.
     * @param  bool|null  $preserveXFA  Keep XFA form data (Adobe dynamic forms). Without it, operations that touch the form remove XFA.
     */
    public static function disk(
        string $path,
        string|null $disk = null,
        ?string $password = null,
        ?bool $preserveXFA = null,
    ): self {
        return new self(Fields::compact([
            'upload' => Upload::fromDisk($path, $disk),
            'password' => $password,
            'preserveXFA' => $preserveXFA,
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
