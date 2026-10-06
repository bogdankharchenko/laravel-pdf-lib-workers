<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Enums\BuiltInFont;
use BogdanKharchenko\PdfMill\Enums\PageNumbersPosition;
use BogdanKharchenko\PdfMill\FontSource;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Writes page numbers.
 */
class PageNumbers extends Data implements Operation
{
    /** Names this step in the operations list: always "pageNumbers". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  string|Optional  $format  Text to write; "{page}" and "{total}" are replaced. Default: "{page} / {total}".
     * @param  PageNumbersPosition|Optional  $position  Default: "bottom-center".
     * @param  float|Optional  $margin  Default: 24.
     * @param  float|Optional  $size  Default: 10.
     * @param  BuiltInFont|FontSource|Optional  $font  A built-in font name, or a font file. Text the font cannot draw is rejected with a 400 that names the characters. Default: "Helvetica".
     * @param  string|Optional  $color  A hex colour: "#rrggbb" or "#rgb". Default: "#000000".
     * @param  int|Optional  $startAt  Number of the first page. Default: 1.
     */
    public function __construct(
        public readonly string|array|Optional $pages = new Optional(),
        public readonly string|Optional $format = new Optional(),
        public readonly PageNumbersPosition|Optional $position = new Optional(),
        public readonly float|Optional $margin = new Optional(),
        public readonly float|Optional $size = new Optional(),
        public readonly BuiltInFont|FontSource|Optional $font = new Optional(),
        public readonly string|Optional $color = new Optional(),
        public readonly int|Optional $startAt = new Optional(),
    ) {
        $this->op = 'pageNumbers';
    }
}
