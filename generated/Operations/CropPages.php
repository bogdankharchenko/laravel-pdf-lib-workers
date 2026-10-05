<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Sets the visible area (crop box). Content outside it is hidden, not removed.
 */
final class CropPages extends Data implements Operation
{
    /** Names this step in the operations list: always "cropPages". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  float  $x
     * @param  float  $y
     * @param  float  $width
     * @param  float  $height
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
