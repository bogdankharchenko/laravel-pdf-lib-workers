<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Enums\BlendMode;
use BogdanKharchenko\PdfMill\Enums\BuiltInFont;
use BogdanKharchenko\PdfMill\Enums\DrawTextRenderMode;
use BogdanKharchenko\PdfMill\Enums\Origin;
use BogdanKharchenko\PdfMill\FontSource;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Draws text.
 */
final class DrawText extends Data implements Operation
{
    /** Names this step in the operations list: always "drawText". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string  $text  The text. "\n" starts a new line.
     * @param  float  $x
     * @param  float  $y  Baseline of the first line (bottom-left origin), or top of the text (top-left origin).
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  float|Optional  $size  Font size in points. Default: 12.
     * @param  BuiltInFont|FontSource|Optional  $font  A built-in font name, or a font file. Text the font cannot draw is rejected with a 400 that names the characters. Default: "Helvetica".
     * @param  string|Optional  $color  A hex colour: "#rrggbb" or "#rgb". Default: "#000000".
     * @param  float|Optional  $opacity  0 (invisible) to 1 (opaque).
     * @param  float|Optional  $rotate  Rotation in degrees, counter-clockwise.
     * @param  float|Optional  $xSkew  Horizontal skew in degrees.
     * @param  float|Optional  $ySkew  Vertical skew in degrees.
     * @param  float|Optional  $maxWidth  Wrap lines at this width, in points.
     * @param  float|Optional  $lineHeight  Distance between baselines. Default: 1.2 × size.
     * @param  list<string>|Optional  $wordBreaks  Characters after which a line may wrap (with maxWidth). Default: [" "].
     * @param  float|Optional  $characterSpacing  Extra space between characters, in points.
     * @param  DrawTextRenderMode|Optional  $renderMode  "invisible" text can still be selected and searched.
     * @param  string|Optional  $strokeColor  Outline colour, for the outline render modes.
     * @param  float|Optional  $strokeWidth  Outline width in points.
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     */
    public function __construct(
        public readonly string $text,
        public readonly float $x,
        public readonly float $y,
        public readonly string|array|Optional $pages = new Optional(),
        public readonly Origin|Optional $origin = new Optional(),
        public readonly float|Optional $size = new Optional(),
        public readonly BuiltInFont|FontSource|Optional $font = new Optional(),
        public readonly string|Optional $color = new Optional(),
        public readonly float|Optional $opacity = new Optional(),
        public readonly float|Optional $rotate = new Optional(),
        public readonly float|Optional $xSkew = new Optional(),
        public readonly float|Optional $ySkew = new Optional(),
        public readonly float|Optional $maxWidth = new Optional(),
        public readonly float|Optional $lineHeight = new Optional(),
        public readonly array|Optional $wordBreaks = new Optional(),
        public readonly float|Optional $characterSpacing = new Optional(),
        public readonly DrawTextRenderMode|Optional $renderMode = new Optional(),
        public readonly string|Optional $strokeColor = new Optional(),
        public readonly float|Optional $strokeWidth = new Optional(),
        public readonly BlendMode|Optional $blendMode = new Optional(),
    ) {
        $this->op = 'drawText';
    }
}
