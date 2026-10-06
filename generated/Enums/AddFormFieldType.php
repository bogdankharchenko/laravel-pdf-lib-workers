<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
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
