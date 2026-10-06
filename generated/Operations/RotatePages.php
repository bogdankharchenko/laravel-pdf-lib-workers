<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Rotates pages by a multiple of 90°.
 */
class RotatePages extends Data implements Operation
{
    /** Names this step in the operations list: always "rotatePages". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  int  $degrees  Clockwise turn: 90, 180, 270 (or negative).
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  bool|Optional  $relative  true adds to the current rotation; false sets it outright. Default: true.
     */
    public function __construct(
        public readonly int $degrees,
        public readonly string|array|Optional $pages = new Optional(),
        public readonly bool|Optional $relative = new Optional(),
    ) {
        $this->op = 'rotatePages';
    }
}
