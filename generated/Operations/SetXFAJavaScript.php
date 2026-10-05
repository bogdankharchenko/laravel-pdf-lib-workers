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
 * Replaces a script in an XFA form. The source needs "preserveXFA": true.
 */
final class SetXFAJavaScript extends Data implements Operation
{
    /** Names this step in the operations list: always "setXFAJavaScript". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string  $field
     * @param  string  $event  XFA event, e.g. "event__click" (see /pdf/scripts).
     * @param  string  $script
     */
    public function __construct(
        public readonly string $field,
        public readonly string $event,
        public readonly string $script,
    ) {
        $this->op = 'setXFAJavaScript';
    }
}
