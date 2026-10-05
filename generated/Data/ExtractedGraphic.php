<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Data;

final class ExtractedGraphic extends Data
{
    /**
     * @param  float  $x
     * @param  float  $y
     * @param  float  $width
     * @param  float  $height
     * @param  string  $svg  Vector graphics, approximated as SVG.
     */
    public function __construct(
        public readonly float $x,
        public readonly float $y,
        public readonly float $width,
        public readonly float $height,
        public readonly string $svg,
    ) {
    }
}
