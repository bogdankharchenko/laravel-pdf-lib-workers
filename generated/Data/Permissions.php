<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
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
final class Permissions extends Data
{
    /**
     * @param  bool|PermissionsPrinting|Optional  $printing
     * @param  bool|Optional  $modifying
     * @param  bool|Optional  $copying
     * @param  bool|Optional  $annotating
     * @param  bool|Optional  $fillingForms
     * @param  bool|Optional  $contentAccessibility
     * @param  bool|Optional  $documentAssembly
     */
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
