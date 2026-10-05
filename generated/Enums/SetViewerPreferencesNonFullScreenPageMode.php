<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Enums;

/**
 * Panel shown after leaving full screen.
 */
enum SetViewerPreferencesNonFullScreenPageMode: string
{
    case UseNone = 'UseNone';
    case UseOutlines = 'UseOutlines';
    case UseThumbs = 'UseThumbs';
    case UseOC = 'UseOC';
}
