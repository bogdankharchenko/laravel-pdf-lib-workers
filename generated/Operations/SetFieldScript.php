<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Enums\SetFieldScriptEvent;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

/**
 * Replaces the script of a field's existing action (see /pdf/scripts). New actions cannot be added.
 */
class SetFieldScript extends Data implements Operation
{
    /** Names this step in the operations list: always "setFieldScript". */
    #[Computed]
    public readonly string $op;

    public function __construct(
        public readonly string $name,
        public readonly SetFieldScriptEvent $event,
        public readonly string $script,
    ) {
        $this->op = 'setFieldScript';
    }
}
