<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Enums;

enum SetViewerPreferencesPageLayout: string
{
    case SinglePage = 'SinglePage';
    case OneColumn = 'OneColumn';
    case TwoColumnLeft = 'TwoColumnLeft';
    case TwoColumnRight = 'TwoColumnRight';
    case TwoPageLeft = 'TwoPageLeft';
    case TwoPageRight = 'TwoPageRight';
}
