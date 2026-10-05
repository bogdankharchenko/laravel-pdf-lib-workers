<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Data;

final class TextItem extends Data
{
    /**
     * @param  string  $text
     * @param  float  $x
     * @param  float  $y
     * @param  float  $fontSize
     * @param  string  $fontFamily
     */
    public function __construct(
        public readonly string $text,
        public readonly float $x,
        public readonly float $y,
        public readonly float $fontSize,
        public readonly string $fontFamily,
    ) {
    }
}
