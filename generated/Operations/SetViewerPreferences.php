<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Enums\SetViewerPreferencesDuplex;
use BogdanKharchenko\PdfLibWorkers\Enums\SetViewerPreferencesNonFullScreenPageMode;
use BogdanKharchenko\PdfLibWorkers\Enums\SetViewerPreferencesPageLayout;
use BogdanKharchenko\PdfLibWorkers\Enums\SetViewerPreferencesPageMode;
use BogdanKharchenko\PdfLibWorkers\Enums\SetViewerPreferencesPrintScaling;
use BogdanKharchenko\PdfLibWorkers\Enums\SetViewerPreferencesReadingDirection;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Controls how viewers open the PDF.
 */
final class SetViewerPreferences extends Data implements Operation
{
    /** Names this step in the operations list: always "setViewerPreferences". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  bool|Optional  $hideToolbar
     * @param  bool|Optional  $hideMenubar
     * @param  bool|Optional  $hideWindowUI
     * @param  bool|Optional  $fitWindow
     * @param  bool|Optional  $centerWindow
     * @param  bool|Optional  $displayDocTitle  Show the title, not the file name, in the window bar.
     * @param  SetViewerPreferencesPageMode|Optional  $pageMode  Which panel is open, or full screen.
     * @param  SetViewerPreferencesPageLayout|Optional  $pageLayout
     * @param  SetViewerPreferencesNonFullScreenPageMode|Optional  $nonFullScreenPageMode  Panel shown after leaving full screen.
     * @param  SetViewerPreferencesReadingDirection|Optional  $readingDirection
     * @param  SetViewerPreferencesPrintScaling|Optional  $printScaling  Print dialog default; "None" prints at actual size.
     * @param  SetViewerPreferencesDuplex|Optional  $duplex
     * @param  bool|Optional  $pickTrayByPDFSize
     * @param  string|list<int>|Optional  $printPageRange  Print dialog's default page range.
     * @param  int|Optional  $numCopies  Print dialog's default number of copies.
     */
    public function __construct(
        public readonly bool|Optional $hideToolbar = new Optional(),
        public readonly bool|Optional $hideMenubar = new Optional(),
        public readonly bool|Optional $hideWindowUI = new Optional(),
        public readonly bool|Optional $fitWindow = new Optional(),
        public readonly bool|Optional $centerWindow = new Optional(),
        public readonly bool|Optional $displayDocTitle = new Optional(),
        public readonly SetViewerPreferencesPageMode|Optional $pageMode = new Optional(),
        public readonly SetViewerPreferencesPageLayout|Optional $pageLayout = new Optional(),
        public readonly SetViewerPreferencesNonFullScreenPageMode|Optional $nonFullScreenPageMode = new Optional(),
        public readonly SetViewerPreferencesReadingDirection|Optional $readingDirection = new Optional(),
        public readonly SetViewerPreferencesPrintScaling|Optional $printScaling = new Optional(),
        public readonly SetViewerPreferencesDuplex|Optional $duplex = new Optional(),
        public readonly bool|Optional $pickTrayByPDFSize = new Optional(),
        public readonly string|array|Optional $printPageRange = new Optional(),
        public readonly int|Optional $numCopies = new Optional(),
    ) {
        $this->op = 'setViewerPreferences';
    }
}
