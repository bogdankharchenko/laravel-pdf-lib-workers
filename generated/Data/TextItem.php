<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Data;

class TextItem extends Data
{
    public function __construct(
        public readonly string $text,
        public readonly float $x,
        public readonly float $y,
        public readonly float $fontSize,
        public readonly string $fontFamily,
    ) {
    }
}
