<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

class InfoResponse extends Data
{
    /**
     * @param  string|null  $pdfA  PDF/A part and level, e.g. "3B", or null.
     * @param  list<PageInfo>  $pages
     * @param  list<Layer>  $layers
     * @param  ViewerPreferences  $viewerPreferences  pageMode and pageLayout are always present; the rest only when the PDF sets viewer preferences.
     * @param  list<AttachmentInfo>  $attachments
     * @param  bool  $hasJavaScript  Whether the PDF has document-level JavaScript; see /pdf/scripts.
     */
    public function __construct(
        public readonly int $pageCount,
        public readonly bool $encrypted,
        public readonly ?string $pdfA,
        public readonly DocumentMetadata $metadata,
        #[DataCollectionOf(PageInfo::class)]
        public readonly array $pages,
        public readonly FormInfo $form,
        #[DataCollectionOf(Layer::class)]
        public readonly array $layers,
        public readonly ViewerPreferences $viewerPreferences,
        #[DataCollectionOf(AttachmentInfo::class)]
        public readonly array $attachments,
        public readonly bool $hasJavaScript,
    ) {
    }
}
