<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Data;

/**
 * The PDF itself, for output.store: false.
 */
class InlinePdf extends Data
{
    /**
     * @param  string  $base64  The file's bytes, base64-encoded.
     * @param  int  $size  File size in bytes.
     */
    public function __construct(
        public readonly string $base64,
        public readonly int $size,
        public readonly int $pageCount,
    ) {
    }
}
