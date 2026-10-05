<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Enums\SetFieldScriptEvent;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

/**
 * Replaces the script of a field's existing action (see /pdf/scripts). New actions cannot be added.
 */
final class SetFieldScript extends Data implements Operation
{
    /** Names this step in the operations list: always "setFieldScript". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string  $name
     * @param  SetFieldScriptEvent  $event
     * @param  string  $script
     */
    public function __construct(
        public readonly string $name,
        public readonly SetFieldScriptEvent $event,
        public readonly string $script,
    ) {
        $this->op = 'setFieldScript';
    }
}
