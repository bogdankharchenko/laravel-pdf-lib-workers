<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Data;

final class FieldScript extends Data
{
    /**
     * @param  string  $event
     * @param  string  $script
     * @param  string  $field
     */
    public function __construct(
        public readonly string $event,
        public readonly string $script,
        public readonly string $field,
    ) {
    }
}
