<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use BogdanKharchenko\PdfMill\Enums\Alignment;
use Spatie\LaravelData\Data;

/**
 * A field's settings; which ones appear depends on the field type.
 */
class FieldSettings extends Data
{
    public function __construct(
        public readonly bool $readOnly,
        public readonly bool $required,
        public readonly bool $exported,
        public readonly ?bool $multiline = null,
        public readonly ?int $maxLength = null,
        public readonly ?Alignment $alignment = null,
        public readonly ?bool $password = null,
        public readonly ?bool $comb = null,
        public readonly ?bool $multiselect = null,
        public readonly ?bool $sort = null,
        public readonly ?bool $editable = null,
        public readonly ?bool $offToggle = null,
        public readonly ?bool $mutuallyExclusive = null,
        public readonly ?bool $checked = null,
    ) {
    }
}
