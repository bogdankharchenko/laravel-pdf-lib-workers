<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class ExtractedPage extends Data
{
    /**
     * @param  string|null  $text  With include "text".
     * @param  list<ExtractedImage>|null  $images  With include "images".
     * @param  list<ExtractedGraphic>|null  $graphics  With include "graphics".
     */
    public function __construct(
        public readonly int $page,
        public readonly ?string $text = null,
        #[DataCollectionOf(ExtractedImage::class)]
        public readonly ?array $images = null,
        #[DataCollectionOf(ExtractedGraphic::class)]
        public readonly ?array $graphics = null,
    ) {
    }
}
