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

/**
 * Removes pages. At least one page must remain.
 */
class RemovePages extends Data implements Operation
{
    /** Names this step in the operations list: always "removePages". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string|list<int>  $pages  Pages to remove.
     */
    public function __construct(
        public readonly string|array $pages,
    ) {
        $this->op = 'removePages';
    }
}
