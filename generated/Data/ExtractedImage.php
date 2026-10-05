<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use BogdanKharchenko\PdfLibWorkers\Enums\ExtractedImageMimeType;
use Spatie\LaravelData\Data;

/**
 * Comes in several shapes; fields not in every shape are null when absent.
 */
final class ExtractedImage extends Data
{
    /**
     * @param  ExtractedImageMimeType  $mimeType
     * @param  int  $width  Pixels.
     * @param  int  $height  Pixels.
     * @param  float  $x
     * @param  float  $y
     * @param  float  $drawWidth  Size drawn on the page, in points.
     * @param  float  $drawHeight
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
