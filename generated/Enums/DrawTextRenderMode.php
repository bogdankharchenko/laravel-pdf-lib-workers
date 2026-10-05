<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Enums;

/**
 * "invisible" text can still be selected and searched.
 */
enum DrawTextRenderMode: string
{
    case Fill = 'fill';
    case Outline = 'outline';
    case FillAndOutline = 'fillAndOutline';
    case Invisible = 'invisible';
}
