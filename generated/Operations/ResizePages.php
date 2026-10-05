<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Enums\PaperSize;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Changes the paper size.
 */
final class ResizePages extends Data implements Operation
{
    /** Names this step in the operations list: always "resizePages". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  PaperSize|array{float, float}  $size  A paper name ("A4", "Letter", "Legal", …) or [width, height] in points (72 pt = 1 inch; A4 is 595 × 842).
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  bool|Optional  $scaleContent  true shrinks or grows the content to fit and centres it; false only changes the paper size. Default: true.
     */
    public function __construct(
        public readonly PaperSize|array $size,
        public readonly string|array|Optional $pages = new Optional(),
        public readonly bool|Optional $scaleContent = new Optional(),
    ) {
        $this->op = 'resizePages';
    }
}
