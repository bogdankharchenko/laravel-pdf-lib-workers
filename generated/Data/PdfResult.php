<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

/**
 * Result of create, edit or merge as JSON (the default; see the Accept header).
 *
 * Reads a response as StoredPdf or InlinePdf.
 */
final class PdfResult
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function from(array $data): StoredPdf|InlinePdf
    {
        return match (true) {
            array_key_exists('key', $data) => StoredPdf::from($data),
            default => InlinePdf::from($data),
        };
    }
}
