<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

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
