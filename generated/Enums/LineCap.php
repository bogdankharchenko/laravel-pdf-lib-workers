<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Enums;

/**
 * Shape of line ends.
 */
enum LineCap: string
{
    case Butt = 'butt';
    case Round = 'round';
    case Projecting = 'projecting';
}
