<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Data\Point;
use BogdanKharchenko\PdfLibWorkers\Enums\BlendMode;
use BogdanKharchenko\PdfLibWorkers\Enums\LineCap;
use BogdanKharchenko\PdfLibWorkers\Enums\Origin;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Draws a straight line.
 */
final class DrawLine extends Data implements Operation
{
    /** Names this step in the operations list: always "drawLine". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  Point  $start  A position in points (72 pt = 1 inch).
     * @param  Point  $end  A position in points (72 pt = 1 inch).
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  float|Optional  $thickness  Default: 1.
     * @param  string|Optional  $color  A hex colour: "#rrggbb" or "#rgb". Default: "#000000".
     * @param  float|Optional  $opacity  0 (invisible) to 1 (opaque).
     * @param  LineCap|Optional  $lineCap  Shape of line ends.
     * @param  list<float>|Optional  $dashArray  Dash pattern, e.g. [6, 3].
     * @param  float|Optional  $dashPhase
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     */
    public function __construct(
        public readonly Point $start,
        public readonly Point $end,
        public readonly string|array|Optional $pages = new Optional(),
        public readonly Origin|Optional $origin = new Optional(),
        public readonly float|Optional $thickness = new Optional(),
        public readonly string|Optional $color = new Optional(),
        public readonly float|Optional $opacity = new Optional(),
        public readonly LineCap|Optional $lineCap = new Optional(),
        public readonly array|Optional $dashArray = new Optional(),
        public readonly float|Optional $dashPhase = new Optional(),
        public readonly BlendMode|Optional $blendMode = new Optional(),
    ) {
        $this->op = 'drawLine';
    }
}
