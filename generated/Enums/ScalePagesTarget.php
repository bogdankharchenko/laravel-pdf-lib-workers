<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Enums;

/**
 * "page" scales the paper, content and form fields together; "content" or "annotations" scale only those.
 */
enum ScalePagesTarget: string
{
    case Page = 'page';
    case Content = 'content';
    case Annotations = 'annotations';
}
