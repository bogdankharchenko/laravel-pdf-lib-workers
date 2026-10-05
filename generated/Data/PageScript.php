<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

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
