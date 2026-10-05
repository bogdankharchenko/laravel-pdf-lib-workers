<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Enums;

/**
 * How the file relates to the PDF (PDF/A-3 associated files).
 */
enum AttachFileRelationship: string
{
    case Source = 'Source';
    case Data = 'Data';
    case Alternative = 'Alternative';
    case Supplement = 'Supplement';
    case EncryptedPayload = 'EncryptedPayload';
    case Schema = 'Schema';
    case Unspecified = 'Unspecified';
}
