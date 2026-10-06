<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use BogdanKharchenko\PdfMill\Enums\ExtractedImageMimeType;
use Spatie\LaravelData\Data;

/**
 * Comes in several shapes; fields not in every shape are null when absent.
 */
class ExtractedImage extends Data
{
    /**
     * @param  int  $width  Pixels.
     * @param  int  $height  Pixels.
     * @param  float  $drawWidth  Size drawn on the page, in points.
     * @param  string|null  $key  R2 key of the saved file.
     * @param  string|null  $url  Signed download link; works without the API key until expiresAt.
     * @param  string|null  $expiresAt  When the link stops working (ISO 8601).
     * @param  string|null  $base64  The file's bytes, base64-encoded.
     */
    public function __construct(
        public readonly ExtractedImageMimeType $mimeType,
        public readonly int $width,
        public readonly int $height,
        public readonly float $x,
        public readonly float $y,
        public readonly float $drawWidth,
        public readonly float $drawHeight,
        public readonly ?string $key = null,
        public readonly ?string $url = null,
        public readonly ?string $expiresAt = null,
        public readonly ?string $base64 = null,
    ) {
    }
}
