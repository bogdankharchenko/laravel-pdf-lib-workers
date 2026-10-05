<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Enums;

enum AddFormFieldType: string
{
    case Text = 'text';
    case Checkbox = 'checkbox';
    case Dropdown = 'dropdown';
    case OptionList = 'optionList';
    case Radio = 'radio';
    case Button = 'button';
}
