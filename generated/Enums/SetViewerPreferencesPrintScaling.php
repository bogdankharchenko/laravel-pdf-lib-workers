<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Enums;

/**
 * Print dialog default; "None" prints at actual size.
 */
enum SetViewerPreferencesPrintScaling: string
{
    case None = 'None';
    case AppDefault = 'AppDefault';
}
