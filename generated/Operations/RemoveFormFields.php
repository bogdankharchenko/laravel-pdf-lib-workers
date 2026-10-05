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
