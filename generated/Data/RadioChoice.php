<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * One radio button: its value and its box.
 */
final class RadioChoice extends Data
{
    /**
     * @param  string  $value
     * @param  float  $x
     * @param  float  $y
     * @param  float  $width
     * @param  float  $height
     * @param  int|Optional  $page  Default: the field's page.
     */
    public function __construct(
        public readonly string $value,
        public readonly float $x,
        public readonly float $y,
        public readonly float $width,
        public readonly float $height,
        public readonly int|Optional $page = new Optional(),
    ) {
    }
}
