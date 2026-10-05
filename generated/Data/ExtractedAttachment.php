<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Data;

/**
 * Comes in several shapes; fields not in every shape are null when absent.
 */
final class ExtractedAttachment extends Data
{
    /**
     * @param  string  $name
     * @param  string|null  $mimeType
     * @param  string|null  $description
     * @param  int  $size
     * @param  string|null  $key  R2 key of the saved file.
     * @param  string|null  $url  Signed download link; works without the API key until expiresAt.
     * @param  string|null  $expiresAt  When the link stops working (ISO 8601).
     * @param  string|null  $base64  The file's bytes, base64-encoded.
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $mimeType,
        public readonly ?string $description,
        public readonly int $size,
        public readonly ?string $key = null,
        public readonly ?string $url = null,
        public readonly ?string $expiresAt = null,
        public readonly ?string $base64 = null,
    ) {
    }
}
