<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use BogdanKharchenko\PdfMill\Enums\PermissionsPrinting;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * What user-password holders may do. Everything is allowed unless set to false.
 */
class Permissions extends Data
{
    public function __construct(
        public readonly bool|PermissionsPrinting|Optional $printing = new Optional(),
        public readonly bool|Optional $modifying = new Optional(),
        public readonly bool|Optional $copying = new Optional(),
        public readonly bool|Optional $annotating = new Optional(),
        public readonly bool|Optional $fillingForms = new Optional(),
        public readonly bool|Optional $contentAccessibility = new Optional(),
        public readonly bool|Optional $documentAssembly = new Optional(),
    ) {
    }
}
