<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Enums\BuiltInFont;
use BogdanKharchenko\PdfMill\FontSource;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Turns all form fields into plain page content.
 */
class FlattenForm extends Data implements Operation
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
