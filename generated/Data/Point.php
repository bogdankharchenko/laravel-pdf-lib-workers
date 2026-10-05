<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

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
