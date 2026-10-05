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
 * Removes form fields.
 */
final class RemoveFormFields extends Data implements Operation
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
