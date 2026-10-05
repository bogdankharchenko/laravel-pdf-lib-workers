<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

/**
 * pageMode and pageLayout are always present; the rest only when the PDF sets viewer preferences.
 */
final class ViewerPreferences extends Data
{
    /**
     * @param  string|null  $pageMode
     * @param  string|null  $pageLayout
     * @param  bool|null  $hideToolbar
     * @param  bool|null  $hideMenubar
     * @param  bool|null  $hideWindowUI
     * @param  bool|null  $fitWindow
     * @param  bool|null  $centerWindow
     * @param  bool|null  $displayDocTitle
     * @param  string|null  $nonFullScreenPageMode
     * @param  string|null  $readingDirection
     * @param  string|null  $printScaling
     * @param  string|null  $duplex
     * @param  bool|null  $pickTrayByPDFSize
     * @param  list<PageRange>|null  $printPageRange
     * @param  int|null  $numCopies
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
