<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Data;

/**
 * 1-based, inclusive.
 */
final class PageRange extends Data
{
    /**
     * @param  int  $start
     * @param  int  $end
     */
    public function __construct(
        public readonly int $start,
        public readonly int $end,
    ) {
    }
}
