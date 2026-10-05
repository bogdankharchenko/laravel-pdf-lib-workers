<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Data;

/**
 * A rectangle by its edges, in the source page's coordinates.
 */
final class ClipBox extends Data
{
    /**
     * @param  float  $left
     * @param  float  $bottom
     * @param  float  $right
     * @param  float  $top
     */
    public function __construct(
        public readonly float $left,
        public readonly float $bottom,
        public readonly float $right,
        public readonly float $top,
    ) {
    }
}
