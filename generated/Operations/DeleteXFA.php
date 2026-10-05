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

/**
 * Removes XFA form data, leaving the regular form fields.
 */
final class DeleteXFA extends Data implements Operation
{
    /** Names this step in the operations list: always "deleteXFA". */
    #[Computed]
    public readonly string $op;

    public function __construct()
    {
        $this->op = 'deleteXFA';
    }
}
