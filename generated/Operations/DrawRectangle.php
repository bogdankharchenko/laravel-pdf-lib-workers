<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Enums\BlendMode;
use BogdanKharchenko\PdfLibWorkers\Enums\LineCap;
use BogdanKharchenko\PdfLibWorkers\Enums\Origin;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Draws a rectangle, optionally with rounded corners.
 */
final class DrawRectangle extends Data implements Operation
{
    /** Names this step in the operations list: always "drawRectangle". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  float  $x
     * @param  float  $y  Bottom edge (bottom-left origin) or top edge (top-left origin).
     * @param  float  $width
     * @param  float  $height
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  float|Optional  $rx  Horizontal corner radius.
     * @param  float|Optional  $ry  Vertical corner radius.
     * @param  float|Optional  $rotate  Rotation in degrees, counter-clockwise.
     * @param  float|Optional  $xSkew  Horizontal skew in degrees.
     * @param  float|Optional  $ySkew  Vertical skew in degrees.
     * @param  string|Optional  $color  Fill colour. Default: no fill.
     * @param  float|Optional  $opacity  0 (invisible) to 1 (opaque).
     * @param  string|Optional  $borderColor  Border colour. Default: no border.
     * @param  float|Optional  $borderWidth  Border width in points. Default: 1 when borderColor is set.
     * @param  float|Optional  $borderOpacity  Border opacity. Default: same as opacity.
     * @param  list<float>|Optional  $borderDashArray  Dash pattern, e.g. [6, 3] = 6 pt dash, 3 pt gap.
     * @param  float|Optional  $borderDashPhase  Offset into the dash pattern.
     * @param  LineCap|Optional  $borderLineCap  Shape of line ends.
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     */
    public function __construct(
        public readonly float $x,
        public readonly float $y,
        public readonly float $width,
        public readonly float $height,
        public readonly string|array|Optional $pages = new Optional(),
        public readonly Origin|Optional $origin = new Optional(),
        public readonly float|Optional $rx = new Optional(),
        public readonly float|Optional $ry = new Optional(),
        public readonly float|Optional $rotate = new Optional(),
        public readonly float|Optional $xSkew = new Optional(),
        public readonly float|Optional $ySkew = new Optional(),
        public readonly string|Optional $color = new Optional(),
        public readonly float|Optional $opacity = new Optional(),
        public readonly string|Optional $borderColor = new Optional(),
        public readonly float|Optional $borderWidth = new Optional(),
        public readonly float|Optional $borderOpacity = new Optional(),
        public readonly array|Optional $borderDashArray = new Optional(),
        public readonly float|Optional $borderDashPhase = new Optional(),
        public readonly LineCap|Optional $borderLineCap = new Optional(),
        public readonly BlendMode|Optional $blendMode = new Optional(),
    ) {
        $this->op = 'drawRectangle';
    }
}
