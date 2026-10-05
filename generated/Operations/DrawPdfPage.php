<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Data\ClipBox;
use BogdanKharchenko\PdfLibWorkers\Enums\BlendMode;
use BogdanKharchenko\PdfLibWorkers\Enums\Origin;
use BogdanKharchenko\PdfLibWorkers\PdfSource;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Draws a page of another PDF onto pages: letterheads, backgrounds, stamps, several pages on one sheet.
 */
final class DrawPdfPage extends Data implements Operation
{
    /** Names this step in the operations list: always "drawPdfPage". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string|PdfSource  $source  The PDF to take the page from.
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  int|Optional  $page  1-based page of the source; negatives count from the end. Default: 1.
     * @param  ClipBox|Optional  $clip  Part of the source page to use, in its own coordinates. Default: the whole page.
     * @param  float|Optional  $x  Default: 0.
     * @param  float|Optional  $y  Bottom edge (bottom-left origin) or top edge (top-left origin). Default: 0.
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  float|Optional  $width  Give one of width/height to keep the aspect ratio.
     * @param  float|Optional  $height
     * @param  float|Optional  $scale  Alternative to width/height: a factor of the source size.
     * @param  float|Optional  $opacity  0 (invisible) to 1 (opaque).
     * @param  float|Optional  $rotate  Rotation in degrees, counter-clockwise.
     * @param  float|Optional  $xSkew  Horizontal skew in degrees.
     * @param  float|Optional  $ySkew  Vertical skew in degrees.
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     * @param  bool|Optional  $behind  Draw under the page's existing content, e.g. a letterhead background. Default: false.
     */
    public function __construct(
        public readonly string|PdfSource $source,
        public readonly string|array|Optional $pages = new Optional(),
        public readonly int|Optional $page = new Optional(),
        public readonly ClipBox|Optional $clip = new Optional(),
        public readonly float|Optional $x = new Optional(),
        public readonly float|Optional $y = new Optional(),
        public readonly Origin|Optional $origin = new Optional(),
        public readonly float|Optional $width = new Optional(),
        public readonly float|Optional $height = new Optional(),
        public readonly float|Optional $scale = new Optional(),
        public readonly float|Optional $opacity = new Optional(),
        public readonly float|Optional $rotate = new Optional(),
        public readonly float|Optional $xSkew = new Optional(),
        public readonly float|Optional $ySkew = new Optional(),
        public readonly BlendMode|Optional $blendMode = new Optional(),
        public readonly bool|Optional $behind = new Optional(),
    ) {
        $this->op = 'drawPdfPage';
    }
}
