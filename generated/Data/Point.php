<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Data;

/**
 * A position in points (72 pt = 1 inch).
 */
final class Point extends Data
{
    /**
     * @param  float  $x
     * @param  float  $y
     */
    public function __construct(
        public readonly float $x,
        public readonly float $y,
    ) {
    }
}
