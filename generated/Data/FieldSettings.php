<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use BogdanKharchenko\PdfLibWorkers\Enums\Alignment;
use Spatie\LaravelData\Data;

/**
 * A field's settings; which ones appear depends on the field type.
 */
final class FieldSettings extends Data
{
    /**
     * @param  bool  $readOnly
     * @param  bool  $required
     * @param  bool  $exported
     * @param  bool|null  $multiline
     * @param  int|null  $maxLength
     * @param  Alignment|null  $alignment
     * @param  bool|null  $password
     * @param  bool|null  $comb
     * @param  bool|null  $multiselect
     * @param  bool|null  $sort
     * @param  bool|null  $editable
     * @param  bool|null  $offToggle
     * @param  bool|null  $mutuallyExclusive
     * @param  bool|null  $checked
     */
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
