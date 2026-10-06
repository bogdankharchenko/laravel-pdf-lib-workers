<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Enums\EmbedFacturXConformanceLevel;
use BogdanKharchenko\PdfMill\Source;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Makes a Factur-X / ZUGFeRD e-invoice: attaches your invoice XML and makes the PDF PDF/A-3. The XML is not generated or checked.
 */
class EmbedFacturX extends Data implements Operation
{
    /** Names this step in the operations list: always "embedFacturX". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string|Source  $xml  The complete Factur-X / ZUGFeRD XML.
     */
    public function __construct(
        public readonly string|Source $xml,
        public readonly EmbedFacturXConformanceLevel|Optional $conformanceLevel = new Optional(),
        public readonly string|Optional $fileName = new Optional(),
        public readonly string|Optional $version = new Optional(),
        public readonly string|Optional $documentType = new Optional(),
        public readonly string|Optional $description = new Optional(),
    ) {
        $this->op = 'embedFacturX';
    }
}
