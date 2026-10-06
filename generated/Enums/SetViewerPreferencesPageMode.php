<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Enums;

/**
 * Which panel is open, or full screen.
 */
enum SetViewerPreferencesPageMode: string
{
    case UseNone = 'UseNone';
    case UseOutlines = 'UseOutlines';
    case UseThumbs = 'UseThumbs';
    case FullScreen = 'FullScreen';
    case UseOC = 'UseOC';
    case UseAttachments = 'UseAttachments';
}
