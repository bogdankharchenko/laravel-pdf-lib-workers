<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use BogdanKharchenko\PdfMill\Enums\SignatureFieldSource;
use Spatie\LaravelData\Data;

class SignatureField extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly SignatureFieldSource $source,
    ) {
    }
}
