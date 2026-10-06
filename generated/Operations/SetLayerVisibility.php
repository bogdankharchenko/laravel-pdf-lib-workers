<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Data\Layer;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

/**
 * Shows or hides layers (optional content groups). See /pdf/info for layer names.
 */
class SetLayerVisibility extends Data implements Operation
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
