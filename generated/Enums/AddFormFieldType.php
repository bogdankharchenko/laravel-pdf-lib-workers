<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Enums;

enum AddFormFieldType: string
{
    case Text = 'text';
    case Checkbox = 'checkbox';
    case Dropdown = 'dropdown';
    case OptionList = 'optionList';
    case Radio = 'radio';
    case Button = 'button';
}
