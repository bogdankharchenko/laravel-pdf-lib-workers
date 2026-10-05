<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Data\Rect;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Sets any of the five page boxes: media (paper), crop (visible area), bleed, trim and art (print production).
 */
final class SetPageBoxes extends Data implements Operation
{
    /** Names this step in the operations list: always "setPageBoxes". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  Rect|Optional  $mediaBox  A box in points: lower-left corner (x, y), width and height.
     * @param  Rect|Optional  $cropBox  A box in points: lower-left corner (x, y), width and height.
     * @param  Rect|Optional  $bleedBox  A box in points: lower-left corner (x, y), width and height.
     * @param  Rect|Optional  $trimBox  A box in points: lower-left corner (x, y), width and height.
     * @param  Rect|Optional  $artBox  A box in points: lower-left corner (x, y), width and height.
     */
    public function __construct(
        public readonly string|array|Optional $pages = new Optional(),
        public readonly Rect|Optional $mediaBox = new Optional(),
        public readonly Rect|Optional $cropBox = new Optional(),
        public readonly Rect|Optional $bleedBox = new Optional(),
        public readonly Rect|Optional $trimBox = new Optional(),
        public readonly Rect|Optional $artBox = new Optional(),
    ) {
        $this->op = 'setPageBoxes';
    }
}
