<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use BogdanKharchenko\PdfMill\Support\Casts\DataListOrScalarCast;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

class ErrorResponse extends Data
{
    /**
     * @param  string  $error  What went wrong. Names the failing input, e.g. sources[1] or operations[2] (removePages).
     * @param  list<FieldError>|string|null  $details  For 400 validation errors: each invalid field.
     */
    public function __construct(
        public readonly string $error,
        #[WithCast(DataListOrScalarCast::class, FieldError::class)]
        public readonly array|string|null $details = null,
    ) {
    }
}
