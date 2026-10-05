<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Enums;

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
