<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Enums\ScalePagesTarget;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Scales pages.
 */
class ScalePages extends Data implements Operation
{
    /** Names this step in the operations list: always "scalePages". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  float|array{float, float}  $factor  One factor for both directions, or [x, y]. 0.5 halves the size.
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     * @param  ScalePagesTarget|Optional  $target  "page" scales the paper, content and form fields together; "content" or "annotations" scale only those. Default: "page".
     */
    public function __construct(
        public readonly float|array $factor,
        public readonly string|array|Optional $pages = new Optional(),
        public readonly ScalePagesTarget|Optional $target = new Optional(),
    ) {
        $this->op = 'scalePages';
    }
}
