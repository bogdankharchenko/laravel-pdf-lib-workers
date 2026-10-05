<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

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
