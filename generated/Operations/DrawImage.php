<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Enums\BlendMode;
use BogdanKharchenko\PdfLibWorkers\Enums\Origin;
use BogdanKharchenko\PdfLibWorkers\Source;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Draws a PNG or JPEG. JPEG photos are turned upright using their EXIF orientation.
 */
final class DrawImage extends Data implements Operation
{
    /** Names this step in the operations list: always "drawImage". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string|Source  $image  A PNG or JPEG.
     * @param  float  $x
     * @param  float  $y  Bottom edge (bottom-left origin) or top edge (top-left origin) of the image.
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  float|Optional  $width  Width in points. Give one of width/height to keep the aspect ratio; neither draws 1 px per point.
     * @param  float|Optional  $height
     * @param  float|Optional  $opacity  0 (invisible) to 1 (opaque).
     * @param  float|Optional  $rotate  Rotation in degrees, counter-clockwise.
     * @param  float|Optional  $xSkew  Horizontal skew in degrees.
     * @param  float|Optional  $ySkew  Vertical skew in degrees.
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     */
    public function __construct(
        public readonly string|Source $image,
        public readonly float $x,
        public readonly float $y,
        public readonly string|array|Optional $pages = new Optional(),
        public readonly Origin|Optional $origin = new Optional(),
        public readonly float|Optional $width = new Optional(),
        public readonly float|Optional $height = new Optional(),
        public readonly float|Optional $opacity = new Optional(),
        public readonly float|Optional $rotate = new Optional(),
        public readonly float|Optional $xSkew = new Optional(),
        public readonly float|Optional $ySkew = new Optional(),
        public readonly BlendMode|Optional $blendMode = new Optional(),
    ) {
        $this->op = 'drawImage';
    }
}
