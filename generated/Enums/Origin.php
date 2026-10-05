<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Enums;

/**
 * How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates.
 */
enum Origin: string
{
    case BottomLeft = 'bottom-left';
    case TopLeft = 'top-left';
}
