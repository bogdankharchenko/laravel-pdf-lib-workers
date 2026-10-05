<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
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
