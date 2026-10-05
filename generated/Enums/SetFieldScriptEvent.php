<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Enums;

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
