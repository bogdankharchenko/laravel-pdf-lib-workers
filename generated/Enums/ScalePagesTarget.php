<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Enums;

/**
 * "page" scales the paper, content and form fields together; "content" or "annotations" scale only those.
 */
enum ScalePagesTarget: string
{
    case Page = 'page';
    case Content = 'content';
    case Annotations = 'annotations';
}
