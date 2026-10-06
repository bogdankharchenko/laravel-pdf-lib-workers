<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Data;

/**
 * Media = paper size; crop = visible area; bleed, trim and art are for print production.
 */
class PageBoxes extends Data
{
    /**
     * @param  Box  $mediaBox  A page box in points: lower-left corner (x, y), width and height.
     * @param  Box  $cropBox  A page box in points: lower-left corner (x, y), width and height.
     * @param  Box  $bleedBox  A page box in points: lower-left corner (x, y), width and height.
     * @param  Box  $trimBox  A page box in points: lower-left corner (x, y), width and height.
     * @param  Box  $artBox  A page box in points: lower-left corner (x, y), width and height.
     */
    public function __construct(
        public readonly Box $mediaBox,
        public readonly Box $cropBox,
        public readonly Box $bleedBox,
        public readonly Box $trimBox,
        public readonly Box $artBox,
    ) {
    }
}
