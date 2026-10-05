<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Data;

/**
 * The PDF itself, for output.store: false.
 */
final class InlinePdf extends Data
{
    /**
     * @param  string  $base64  The file's bytes, base64-encoded.
     * @param  int  $size  File size in bytes.
     * @param  int  $pageCount
     */
    public function __construct(
        public readonly string $base64,
        public readonly int $size,
        public readonly int $pageCount,
    ) {
    }
}
