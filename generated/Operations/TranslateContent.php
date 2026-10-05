<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Moves everything drawn on the page by (x, y) points.
 */
final class TranslateContent extends Data implements Operation
{
    /** Names this step in the operations list: always "translateContent". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  float  $x
     * @param  float  $y
     * @param  string|list<int>|Optional  $pages  Pages to apply this to. Default: every page.
     */
    public function __construct(
        public readonly float $x,
        public readonly float $y,
        public readonly string|array|Optional $pages = new Optional(),
    ) {
        $this->op = 'translateContent';
    }
}
