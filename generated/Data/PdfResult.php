<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

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
