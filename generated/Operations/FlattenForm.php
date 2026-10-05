<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Enums\BuiltInFont;
use BogdanKharchenko\PdfLibWorkers\FontSource;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Turns all form fields into plain page content.
 */
final class FlattenForm extends Data implements Operation
{
    /** Names this step in the operations list: always "flattenForm". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  BuiltInFont|FontSource|Optional  $font  Font used to draw the values. Default: Helvetica.
     */
    public function __construct(
        public readonly BuiltInFont|FontSource|Optional $font = new Optional(),
    ) {
        $this->op = 'flattenForm';
    }
}
