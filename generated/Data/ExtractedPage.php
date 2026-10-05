<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

final class ExtractedPage extends Data
{
    /**
     * @param  int  $page
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
