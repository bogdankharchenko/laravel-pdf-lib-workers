<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

/**
 * Result of create, edit or merge as JSON (the default; see the Accept header).
 *
 * Reads a response as StoredPdf or InlinePdf or UploadedPdf.
 */
class PdfResult
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): StoredPdf|InlinePdf|UploadedPdf
    {
        return match (true) {
            array_key_exists('key', $data) => StoredPdf::from($data),
            array_key_exists('base64', $data) => InlinePdf::from($data),
            default => UploadedPdf::from($data),
        };
    }
}
