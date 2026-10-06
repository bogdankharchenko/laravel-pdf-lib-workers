<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Enums\BlendMode;
use BogdanKharchenko\PdfMill\Enums\LineCap;
use BogdanKharchenko\PdfMill\Enums\Origin;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Draws an ellipse or circle centred on (x, y).
 */
class DrawEllipse extends Data implements Operation
{
    /** Names this step in the operations list: always "drawEllipse". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  float|Optional  $yRadius  Default: xRadius (a circle).
     * @param  float|Optional  $rotate  Rotation in degrees, counter-clockwise.
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
        public readonly float $xRadius,
        public readonly string|array|Optional $pages = new Optional(),
        public readonly Origin|Optional $origin = new Optional(),
        public readonly float|Optional $yRadius = new Optional(),
        public readonly float|Optional $rotate = new Optional(),
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
        $this->op = 'drawEllipse';
    }
}
