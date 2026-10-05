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
use BogdanKharchenko\PdfLibWorkers\Enums\Position;
use BogdanKharchenko\PdfLibWorkers\FontSource;
use BogdanKharchenko\PdfLibWorkers\Source;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Stamps text or an image (give one) on each page, centred or in a corner, at any angle and opacity.
 */
final class Watermark extends Data implements Operation
{
    /** Names this step in the operations list: always "watermark". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  string|Optional  $text  Text to stamp. Give text or image.
     * @param  string|Source|Optional  $image  A PNG or JPEG to stamp, e.g. a logo. Give text or image.
     * @param  float|Optional  $scale  Images: width as a share of the page width. Default: 0.5.
     * @param  float|Optional  $size  Text: font size. Default: 60.
     * @param  BuiltInFont|FontSource|Optional  $font  A built-in font name, or a font file. Text the font cannot draw is rejected with a 400 that names the characters. Default: "Helvetica-Bold".
     * @param  string|Optional  $color  A hex colour: "#rrggbb" or "#rgb". Default: "#888888".
     * @param  float|Optional  $opacity  0 (invisible) to 1 (opaque). Default: 0.25.
     * @param  float|Optional  $rotate  Degrees, counter-clockwise. Default: 45 for text, 0 for images.
     * @param  Position|Optional  $position  Where on the page an item is placed. Default: "center".
     * @param  float|Optional  $margin  Distance from the page edge, for corner positions. Default: 24.
     * @param  BlendMode|Optional  $blendMode  How the drawing's colours mix with what is underneath.
     */
    public function __construct(
        public readonly string|array|Optional $pages = new Optional(),
        public readonly string|Optional $text = new Optional(),
        public readonly string|Source|Optional $image = new Optional(),
        public readonly float|Optional $scale = new Optional(),
        public readonly float|Optional $size = new Optional(),
        public readonly BuiltInFont|FontSource|Optional $font = new Optional(),
        public readonly string|Optional $color = new Optional(),
        public readonly float|Optional $opacity = new Optional(),
        public readonly float|Optional $rotate = new Optional(),
        public readonly Position|Optional $position = new Optional(),
        public readonly float|Optional $margin = new Optional(),
        public readonly BlendMode|Optional $blendMode = new Optional(),
    ) {
        $this->op = 'watermark';
    }
}
