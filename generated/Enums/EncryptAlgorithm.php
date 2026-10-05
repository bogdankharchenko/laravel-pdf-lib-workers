<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Enums;

enum EncryptAlgorithm: string
{
    case Aes256 = 'AES-256';
    case Aes128 = 'AES-128';
    case RC4_128 = 'RC4-128';
    case RC4_40 = 'RC4-40';
}
