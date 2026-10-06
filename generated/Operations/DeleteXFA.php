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
 * Removes XFA form data, leaving the regular form fields.
 */
class DeleteXFA extends Data implements Operation
{
    /** Names this step in the operations list: always "deleteXFA". */
    #[Computed]
    public readonly string $op;

    public function __construct()
    {
        $this->op = 'deleteXFA';
    }
}
