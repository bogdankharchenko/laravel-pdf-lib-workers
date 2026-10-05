<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

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
