<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

/**
 * Keeps only these pages, in this order: use it to extract, reorder, reverse or repeat pages.
 */
final class SelectPages extends Data implements Operation
{
    /** Names this step in the operations list: always "selectPages". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string|list<int>  $pages  Pages to keep, in the new order. Repeats are allowed, e.g. "1,1,2".
     */
    public function __construct(
        public readonly string|array $pages,
    ) {
        $this->op = 'selectPages';
    }
}
