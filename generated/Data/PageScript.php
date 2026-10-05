<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Data;

final class PageScript extends Data
{
    /**
     * @param  string  $event
     * @param  string  $script
     * @param  int  $page
     */
    public function __construct(
        public readonly string $event,
        public readonly string $script,
        public readonly int $page,
    ) {
    }
}
