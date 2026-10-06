<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Enums\PaperSize;
use BogdanKharchenko\PdfMill\PdfSource;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Inserts pages from another PDF, or a PNG/JPEG image as a new page.
 */
class InsertPdf extends Data implements Operation
{
    /** Names this step in the operations list: always "insertPdf". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string|PdfSource  $source  The PDF or image to insert.
     * @param  string|list<int>|Optional  $pages  PDFs only: which pages to insert. Default: all.
     * @param  int|Optional  $at  1-based position to insert at. Default: after the last page.
     * @param  PaperSize|array{float, float}|'image'|Optional  $size  Images only: paper to fit the image on (turned landscape for wide images), or "image" for a page the size of the image (1 px = 1 pt). Default: "A4".
     * @param  float|Optional  $margin  Images only: space around the image, in points. Default: 0.
     */
    public function __construct(
        public readonly string|PdfSource $source,
        public readonly string|array|Optional $pages = new Optional(),
        public readonly int|Optional $at = new Optional(),
        public readonly PaperSize|array|string|Optional $size = new Optional(),
        public readonly float|Optional $margin = new Optional(),
    ) {
        $this->op = 'insertPdf';
    }
}
