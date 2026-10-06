<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class ExtractResponse extends Data
{
    /**
     * @param  list<ExtractedPage>  $pages
     * @param  list<ExtractedAttachment>|null  $attachments  With include "attachments".
     */
    public function __construct(
        #[DataCollectionOf(ExtractedPage::class)]
        public readonly array $pages,
        #[DataCollectionOf(ExtractedAttachment::class)]
        public readonly ?array $attachments = null,
    ) {
    }
}
