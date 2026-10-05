<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Enums;

enum SetFieldScriptEvent: string
{
    case Keystroke = 'keystroke';
    case Format = 'format';
    case Validate = 'validate';
    case Calculate = 'calculate';
    case MouseUp = 'mouseUp';
    case MouseDown = 'mouseDown';
    case MouseEnter = 'mouseEnter';
    case MouseExit = 'mouseExit';
    case Focus = 'focus';
    case Blur = 'blur';
}
