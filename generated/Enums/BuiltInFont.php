<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Enums;

/**
 * One of the 14 standard PDF fonts. They cover Latin text only; use a FontSource for anything else.
 */
enum BuiltInFont: string
{
    case Courier = 'Courier';
    case CourierBold = 'Courier-Bold';
    case CourierOblique = 'Courier-Oblique';
    case CourierBoldOblique = 'Courier-BoldOblique';
    case Helvetica = 'Helvetica';
    case HelveticaBold = 'Helvetica-Bold';
    case HelveticaOblique = 'Helvetica-Oblique';
    case HelveticaBoldOblique = 'Helvetica-BoldOblique';
    case TimesRoman = 'Times-Roman';
    case TimesBold = 'Times-Bold';
    case TimesItalic = 'Times-Italic';
    case TimesBoldItalic = 'Times-BoldItalic';
    case Symbol = 'Symbol';
    case ZapfDingbats = 'ZapfDingbats';
}
