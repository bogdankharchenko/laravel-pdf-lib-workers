<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Data;

/**
 * A page box in points: lower-left corner (x, y), width and height.
 */
final class Box extends Data
{
    /**
     * @param  float  $x
     * @param  float  $y
     * @param  float  $width
     * @param  float  $height
     */
    public function __construct(
        public readonly float $x,
        public readonly float $y,
        public readonly float $width,
        public readonly float $height,
    ) {
    }
}
