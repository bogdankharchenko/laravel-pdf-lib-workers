<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

final class FormInfo extends Data
{
    /**
     * @param  bool  $hasXFA
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
