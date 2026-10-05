<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Enums;

enum EncryptAlgorithm: string
{
    case Aes256 = 'AES-256';
    case Aes128 = 'AES-128';
    case RC4_128 = 'RC4-128';
    case RC4_40 = 'RC4-40';
}
