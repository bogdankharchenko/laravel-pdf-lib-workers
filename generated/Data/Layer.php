<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Data;

/**
 * A layer (optional content group) and whether it is shown.
 */
final class Layer extends Data
{
    /**
     * @param  string  $name
     * @param  bool  $visible
     */
    public function __construct(
        public readonly string $name,
        public readonly bool $visible,
    ) {
    }
}
