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
 * Removes form fields.
 */
class RemoveFormFields extends Data implements Operation
{
    /** Names this step in the operations list: always "removeFormFields". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  list<string>  $names
     */
    public function __construct(
        public readonly array $names,
    ) {
        $this->op = 'removeFormFields';
    }
}
