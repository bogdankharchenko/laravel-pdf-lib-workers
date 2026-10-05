<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Enums;

enum SetViewerPreferencesDuplex: string
{
    case Simplex = 'Simplex';
    case DuplexFlipShortEdge = 'DuplexFlipShortEdge';
    case DuplexFlipLongEdge = 'DuplexFlipLongEdge';
}
