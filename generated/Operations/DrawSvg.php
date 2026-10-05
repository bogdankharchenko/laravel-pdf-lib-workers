<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Enums\BlendMode;
use BogdanKharchenko\PdfLibWorkers\Enums\BuiltInFont;
use BogdanKharchenko\PdfLibWorkers\Enums\Origin;
use BogdanKharchenko\PdfLibWorkers\FontSource;
use BogdanKharchenko\PdfLibWorkers\Support\Transformers\JsonObjectTransformer;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Draws an SVG document (shapes, text, transforms).
 */
final class DrawSvg extends Data implements Operation
{
    /** Names this step in the operations list: always "drawSvg". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string  $svg  SVG markup.
     * @param  float  $x
     * @param  float  $y  Top-left corner of the SVG.
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  float|Optional  $width
     * @param  float|Optional  $height
     * @param  float|Optional  $fontSize  Default size for SVG text.
     * @param  array<array-key, BuiltInFont|FontSource>|Optional  $fonts  Fonts for SVG text, keyed by the font-family name used in the SVG.
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     */
    public function __construct(
        public readonly string $svg,
        public readonly float $x,
        public readonly float $y,
        public readonly string|array|Optional $pages = new Optional(),
        public readonly Origin|Optional $origin = new Optional(),
        public readonly float|Optional $width = new Optional(),
        public readonly float|Optional $height = new Optional(),
        public readonly float|Optional $fontSize = new Optional(),
        #[WithTransformer(JsonObjectTransformer::class)]
        public readonly array|Optional $fonts = new Optional(),
        public readonly BlendMode|Optional $blendMode = new Optional(),
    ) {
        $this->op = 'drawSvg';
    }
}
