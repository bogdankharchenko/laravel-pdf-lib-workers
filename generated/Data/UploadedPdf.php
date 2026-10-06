<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Data;

/**
 * The PDF was uploaded to output.put.url.
 */
class UploadedPdf extends Data
{
    /**
     * @param  int  $size  File size in bytes.
     */
    public function __construct(
        public readonly int $size,
        public readonly int $pageCount,
    ) {
    }
}
