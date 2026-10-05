<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Enums;

/**
 * Print dialog default; "None" prints at actual size.
 */
enum SetViewerPreferencesPrintScaling: string
{
    case None = 'None';
    case AppDefault = 'AppDefault';
}
