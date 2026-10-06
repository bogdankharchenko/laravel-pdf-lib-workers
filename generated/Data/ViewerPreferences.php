<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

/**
 * pageMode and pageLayout are always present; the rest only when the PDF sets viewer preferences.
 */
class ViewerPreferences extends Data
{
    /**
     * @param  list<PageRange>|null  $printPageRange
     */
    public function __construct(
        public readonly ?string $pageMode,
        public readonly ?string $pageLayout,
        public readonly ?bool $hideToolbar = null,
        public readonly ?bool $hideMenubar = null,
        public readonly ?bool $hideWindowUI = null,
        public readonly ?bool $fitWindow = null,
        public readonly ?bool $centerWindow = null,
        public readonly ?bool $displayDocTitle = null,
        public readonly ?string $nonFullScreenPageMode = null,
        public readonly ?string $readingDirection = null,
        public readonly ?string $printScaling = null,
        public readonly ?string $duplex = null,
        public readonly ?bool $pickTrayByPDFSize = null,
        #[DataCollectionOf(PageRange::class)]
        public readonly ?array $printPageRange = null,
        public readonly ?int $numCopies = null,
    ) {
    }
}
