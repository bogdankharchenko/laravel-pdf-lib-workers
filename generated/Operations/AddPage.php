<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Enums\PaperSize;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Adds blank pages.
 */
final class AddPage extends Data implements Operation
{
    /** Names this step in the operations list: always "addPage". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  PaperSize|array{float, float}|Optional  $size  A paper name ("A4", "Letter", "Legal", …) or [width, height] in points (72 pt = 1 inch; A4 is 595 × 842). Default: "A4".
     * @param  int|Optional  $at  1-based position to insert at. Default: after the last page.
     * @param  int|Optional  $count  How many pages to add. Default: 1.
     */
    public function __construct(
        public readonly PaperSize|array|Optional $size = new Optional(),
        public readonly int|Optional $at = new Optional(),
        public readonly int|Optional $count = new Optional(),
    ) {
        $this->op = 'addPage';
    }
}
