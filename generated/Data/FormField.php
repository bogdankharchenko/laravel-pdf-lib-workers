<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use BogdanKharchenko\PdfLibWorkers\Enums\FormFieldType;
use Spatie\LaravelData\Data;

final class FormField extends Data
{
    /**
     * @param  string  $name
     * @param  FormFieldType  $type
     * @param  string|bool|list<string>|null  $value  text: string or null; checkbox: boolean; dropdown/optionList: selected options; radio: selected option or null; others: null.
     * @param  FieldSettings  $settings  A field's settings; which ones appear depends on the field type.
     * @param  list<string>|null  $options  Choices, for dropdowns, option lists and radio groups.
     */
    public function __construct(
        public readonly string $name,
        public readonly FormFieldType $type,
        public readonly string|bool|array|null $value,
        public readonly FieldSettings $settings,
        public readonly ?array $options = null,
    ) {
    }
}
