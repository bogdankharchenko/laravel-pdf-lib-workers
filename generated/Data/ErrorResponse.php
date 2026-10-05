<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use BogdanKharchenko\PdfLibWorkers\Support\Casts\DataListOrScalarCast;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;

final class ErrorResponse extends Data
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
