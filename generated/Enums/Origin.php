<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Enums;

/**
 * How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates.
 */
enum Origin: string
{
    case BottomLeft = 'bottom-left';
    case TopLeft = 'top-left';
}
