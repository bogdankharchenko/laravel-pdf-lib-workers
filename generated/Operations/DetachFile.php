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
 * Removes an embedded file.
 */
final class DetachFile extends Data implements Operation
{
    /** Names this step in the operations list: always "detachFile". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string  $name
     */
    public function __construct(
        public readonly string $name,
    ) {
        $this->op = 'detachFile';
    }
}
