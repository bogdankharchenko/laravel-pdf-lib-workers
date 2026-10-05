<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Enums;

/**
 * How the drawing's colours mix with what is underneath.
 */
enum BlendMode: string
{
    case Normal = 'Normal';
    case Multiply = 'Multiply';
    case Screen = 'Screen';
    case Overlay = 'Overlay';
    case Darken = 'Darken';
    case Lighten = 'Lighten';
    case ColorDodge = 'ColorDodge';
    case ColorBurn = 'ColorBurn';
    case HardLight = 'HardLight';
    case SoftLight = 'SoftLight';
    case Difference = 'Difference';
    case Exclusion = 'Exclusion';
}
