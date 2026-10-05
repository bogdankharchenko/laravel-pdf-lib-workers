<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Data\Layer;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

/**
 * Shows or hides layers (optional content groups). See /pdf/info for layer names.
 */
final class SetLayerVisibility extends Data implements Operation
{
    /** Names this step in the operations list: always "setLayerVisibility". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  list<Layer>  $layers
     */
    public function __construct(
        public readonly array $layers,
    ) {
        $this->op = 'setLayerVisibility';
    }
}
