<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Sets the visible area (crop box). Content outside it is hidden, not removed.
 */
class CropPages extends Data implements Operation
{
    /** Names this step in the operations list: always "cropPages". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     */
    public function __construct(
        public readonly float $x,
        public readonly float $y,
        public readonly float $width,
        public readonly float $height,
        public readonly string|array|Optional $pages = new Optional(),
    ) {
        $this->op = 'cropPages';
    }
}
