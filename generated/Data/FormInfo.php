<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class FormInfo extends Data
{
    /**
     * @param  list<FormField>  $fields
     * @param  list<SignatureField>  $signatureFields
     */
    public function __construct(
        public readonly bool $hasXFA,
        #[DataCollectionOf(FormField::class)]
        public readonly array $fields,
        #[DataCollectionOf(SignatureField::class)]
        public readonly array $signatureFields,
    ) {
    }
}
