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
