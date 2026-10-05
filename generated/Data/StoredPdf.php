<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Data;

/**
 * The PDF was saved to R2.
 */
final class StoredPdf extends Data
{
    /**
     * @param  string  $key  R2 key of the saved file.
     * @param  string  $url  Signed download link; works without the API key until expiresAt.
     * @param  string  $expiresAt  When the link stops working (ISO 8601).
     * @param  int  $size  File size in bytes.
     * @param  int  $pageCount
     */
    public function __construct(
        public readonly string $key,
        public readonly string $url,
        public readonly string $expiresAt,
        public readonly int $size,
        public readonly int $pageCount,
    ) {
    }
}
