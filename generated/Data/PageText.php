<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class PageText extends Data
{
    /**
     * @param  string  $text  Text in drawing order, with a new line where the baseline moves. Scanned pages have none (no OCR).
     * @param  list<TextItem>|null  $items  Only with "items": true.
     */
    public function __construct(
        public readonly int $page,
        public readonly string $text,
        #[DataCollectionOf(TextItem::class)]
        public readonly ?array $items = null,
    ) {
    }
}
