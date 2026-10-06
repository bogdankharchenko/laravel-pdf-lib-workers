<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

/**
 * Replaces a script in an XFA form. The source needs "preserveXFA": true.
 */
class SetXFAJavaScript extends Data implements Operation
{
    /** Names this step in the operations list: always "setXFAJavaScript". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string  $event  XFA event, e.g. "event__click" (see /pdf/scripts).
     */
    public function __construct(
        public readonly string $field,
        public readonly string $event,
        public readonly string $script,
    ) {
        $this->op = 'setXFAJavaScript';
    }
}
