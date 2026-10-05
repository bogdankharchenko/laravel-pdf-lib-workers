<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Data;

final class AttachmentInfo extends Data
{
    /**
     * @param  string  $name
     * @param  int  $size
     * @param  string|null  $mimeType
     * @param  string|null  $description
     * @param  string|null  $relationship
     */
    public function __construct(
        public readonly string $name,
        public readonly int $size,
        public readonly ?string $mimeType,
        public readonly ?string $description,
        public readonly ?string $relationship,
    ) {
    }
}
