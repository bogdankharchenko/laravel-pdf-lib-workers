<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Enums;

enum EmbedFacturXConformanceLevel: string
{
    case Minimum = 'MINIMUM';
    case BasicWl = 'BASIC WL';
    case Basic = 'BASIC';
    case En16931 = 'EN 16931';
    case Extended = 'EXTENDED';
    case Xrechnung = 'XRECHNUNG';
}
