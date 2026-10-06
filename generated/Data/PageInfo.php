<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Data;

class PageInfo extends Data
{
    /**
     * @param  int  $page  1-based page number.
     * @param  float  $width  Width in points.
     * @param  float  $height  Height in points.
     * @param  int  $rotation  Clockwise rotation: 0, 90, 180 or 270.
     * @param  PageBoxes  $boxes  Media = paper size; crop = visible area; bleed, trim and art are for print production.
     */
    public function __construct(
        public readonly int $page,
        public readonly float $width,
        public readonly float $height,
        public readonly int $rotation,
        public readonly PageBoxes $boxes,
    ) {
    }
}
