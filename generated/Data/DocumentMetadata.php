<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Data;

final class DocumentMetadata extends Data
{
    /**
     * @param  string|null  $title
     * @param  string|null  $author
     * @param  string|null  $subject
     * @param  string|null  $keywords
     * @param  string|null  $creator
     * @param  string|null  $producer
     * @param  string|null  $language
     * @param  string|null  $creationDate
     * @param  string|null  $modificationDate
     * @param  string|null  $copyright
     * @param  string|null  $copyrightUrl
     * @param  array<array-key, string>  $custom  Custom fields set with setMetadata (or by other tools).
     */
    public function __construct(
        public readonly ?string $title,
        public readonly ?string $author,
        public readonly ?string $subject,
        public readonly ?string $keywords,
        public readonly ?string $creator,
        public readonly ?string $producer,
        public readonly ?string $language,
        public readonly ?string $creationDate,
        public readonly ?string $modificationDate,
        public readonly ?string $copyright,
        public readonly ?string $copyrightUrl,
        public readonly array $custom,
    ) {
    }
}
