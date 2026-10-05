<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Enums;

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
