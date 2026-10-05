<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Data;

final class FieldError extends Data
{
    /**
     * @param  string  $path
     * @param  string  $message
     */
    public function __construct(
        public readonly string $path,
        public readonly string $message,
    ) {
    }
}
