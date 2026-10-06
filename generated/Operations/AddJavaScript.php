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
 * Adds document-level JavaScript, run when the PDF opens in viewers that allow it.
 */
class AddJavaScript extends Data implements Operation
{
    /** Names this step in the operations list: always "addJavaScript". */
    #[Computed]
    public readonly string $op;

    public function __construct(
        public readonly string $name,
        public readonly string $script,
    ) {
        $this->op = 'addJavaScript';
    }
}
