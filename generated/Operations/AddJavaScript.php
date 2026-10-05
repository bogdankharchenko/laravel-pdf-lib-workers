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
 * Adds document-level JavaScript, run when the PDF opens in viewers that allow it.
 */
final class AddJavaScript extends Data implements Operation
{
    /** Names this step in the operations list: always "addJavaScript". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string  $name
     * @param  string  $script
     */
    public function __construct(
        public readonly string $name,
        public readonly string $script,
    ) {
        $this->op = 'addJavaScript';
    }
}
