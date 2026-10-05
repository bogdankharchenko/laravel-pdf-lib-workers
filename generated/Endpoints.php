<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill;

use BogdanKharchenko\PdfMill\Data\ExtractResponse;
use BogdanKharchenko\PdfMill\Data\InfoResponse;
use BogdanKharchenko\PdfMill\Data\LockedInfoResponse;
use BogdanKharchenko\PdfMill\Data\MeasureResponse;
use BogdanKharchenko\PdfMill\Data\ScriptsResponse;
use BogdanKharchenko\PdfMill\Data\SplitResponse;
use BogdanKharchenko\PdfMill\Data\TextResponse;
use BogdanKharchenko\PdfMill\Enums\BuiltInFont;
use BogdanKharchenko\PdfMill\Enums\ExtractInclude;
use BogdanKharchenko\PdfMill\Enums\PaperSize;
use BogdanKharchenko\PdfMill\Exceptions\ApiException;

/**
 * The API's endpoints, one method each. Used by Client.
 */
trait Endpoints
{
    /**
     * Download a stored result
     *
     * Use the signed `url` from a response (no API key needed until it expires), or send the API key with just the path.
     *
     * @param  string  $key  The R2 key, slashes included (e.g. "outputs/abc.pdf"). "%2F" is accepted for "/".
     * @return FileResponse
     *
     * @throws ApiException
     */
    public function download(string $key): FileResponse
    {
        return FileResponse::fromResponse($this->get('/files/'.self::pathParam($key), '*/*'), basename($key));
    }

    /**
     * Pages, boxes, metadata, form fields, layers, viewer preferences, attachments
     *
     * Reads everything about a PDF except its content: pages and their boxes, metadata (including copyright and custom fields), form fields with their types, choices and settings, layers, viewer preferences and attachments. Call it before editing to learn field names and page sizes. An encrypted PDF sent without its password returns a LockedInfoResponse, not an error.
     *
     * @param  string|PdfSource  $source  A PDF: a PdfSourceObject or a shortcut string.
     * @return InfoResponse|LockedInfoResponse
     *
     * @throws ApiException
     */
    public function info(string|PdfSource $source): InfoResponse|LockedInfoResponse
    {
        $data = $this->post('/pdf/info', [
            'source' => $source,
        ])->json();

        return match (true) {
            array_key_exists('pdfA', $data) => InfoResponse::from($data),
            default => LockedInfoResponse::from($data),
        };
    }

    /**
     * Text per page
     *
     * Extracts the text of each page, in drawing order. Set "items": true to also get each run's position, size and font. Scanned pages contain no text: there is no OCR.
     *
     * @param  string|PdfSource  $source  A PDF: a PdfSourceObject or a shortcut string.
     * @param  string|list<int>|null  $pages  Pages, 1-based. A string such as "1-3,5", "first", "last", "odd", "even", "all" or "5-1" (reversed), or an array of numbers where negatives count from the end (-1 = last page). Leaving it out means every page.
     * @param  bool|null  $items  Also return each text run with its position, size and font. Default: false.
     * @return TextResponse
     *
     * @throws ApiException
     */
    public function text(string|PdfSource $source, string|array|null $pages = null, ?bool $items = null): TextResponse
    {
        return TextResponse::from($this->post('/pdf/text', [
            'source' => $source,
            'pages' => $pages,
            'items' => $items,
        ])->json());
    }

    /**
     * Images, vector graphics, text and attachments
     *
     * Pulls images (as PNG or JPEG), vector graphics (approximated as SVG), text and embedded files out of a PDF; choose which with "include". Files are saved to R2 and returned as signed links, or returned as base64 with "store": false.
     *
     * @param  string|PdfSource  $source  A PDF: a PdfSourceObject or a shortcut string.
     * @param  string|list<int>|null  $pages  Pages, 1-based. A string such as "1-3,5", "first", "last", "odd", "even", "all" or "5-1" (reversed), or an array of numbers where negatives count from the end (-1 = last page). Leaving it out means every page.
     * @param  list<ExtractInclude>|null  $include  What to extract. Each choice adds that field to the response ("attachments" at the top level, the rest per page). Default: ["images","attachments"].
     * @param  bool|null  $store  Save images and attachments to R2 and return signed links; false returns them as base64. Default: true.
     * @param  string|null  $prefix  R2 key prefix for stored files. Default: "extracted/<uuid>/", which the recommended expiry rule deletes after 7 days.
     * @param  int|null  $linkTtl  Lifetime of the signed download link, in seconds (max 604800 = 7 days). Default: the SIGNED_URL_TTL setting (3600).
     * @return ExtractResponse
     *
     * @throws ApiException
     */
    public function extract(
        string|PdfSource $source,
        string|array|null $pages = null,
        ?array $include = null,
        ?bool $store = null,
        ?string $prefix = null,
        ?int $linkTtl = null,
    ): ExtractResponse {
        return ExtractResponse::from($this->post('/pdf/extract', [
            'source' => $source,
            'pages' => $pages,
            'include' => $include,
            'store' => $store,
            'prefix' => $prefix,
            'linkTtl' => $linkTtl,
        ])->json());
    }

    /**
     * Document, form field, page and XFA JavaScript
     *
     * Lists the JavaScript in a PDF: document-level scripts, form field actions, page open/close actions and XFA scripts. Use it to find the field and event names that setFieldScript and setXFAJavaScript need.
     *
     * @param  string|PdfSource  $source  A PDF: a PdfSourceObject or a shortcut string.
     * @return ScriptsResponse
     *
     * @throws ApiException
     */
    public function scripts(string|PdfSource $source): ScriptsResponse
    {
        return ScriptsResponse::from($this->post('/pdf/scripts', [
            'source' => $source,
        ])->json());
    }

    /**
     * Make a new PDF
     *
     * Makes a new PDF from blank pages and an operations list: text, images, shapes, other PDFs' pages, form fields, metadata, encryption.
     *
     * Chain PendingPdf's methods to add operations, then call store(), file() or download(), or return it from a route.
     *
     * @param  PaperSize|array{float, float}|null  $size  A paper name ("A4", "Letter", "Legal", …) or [width, height] in points (72 pt = 1 inch; A4 is 595 × 842). Default: "A4".
     * @param  int|null  $pageCount  Blank pages to start with. With 0, add pages with addPage. Default: 1.
     * @return PendingPdf
     */
    public function create(PaperSize|array|null $size = null, ?int $pageCount = null): PendingPdf
    {
        return new PendingPdf(fn (array $body, string $accept) => $this->post('/pdf/create', $body, $accept), [
            'size' => $size,
            'pageCount' => $pageCount,
        ]);
    }

    /**
     * Run operations on a PDF
     *
     * Runs an operations list on one PDF. With "incremental": true the original bytes are kept and the changes appended, so existing digital signatures stay valid. A PDF opened with its password is saved without one unless the operations include encrypt.
     *
     * Chain PendingPdf's methods to add operations, then call store(), file() or download(), or return it from a route.
     *
     * @param  string|PdfSource  $source  A PDF: a PdfSourceObject or a shortcut string.
     * @param  bool|null  $incremental  Keep the original bytes and append the changes, so existing digital signatures stay valid. Default: false.
     * @return PendingPdf
     */
    public function edit(string|PdfSource $source, ?bool $incremental = null): PendingPdf
    {
        return new PendingPdf(fn (array $body, string $accept) => $this->post('/pdf/edit', $body, $accept), [
            'source' => $source,
            'incremental' => $incremental,
        ]);
    }

    /**
     * Join PDFs and images, then run operations
     *
     * Joins PDFs (whole or chosen pages) and PNG/JPEG images in order, each image becoming one page, then runs an optional operations list on the result. In a multipart request "sources" may be left out: every uploaded PDF and image is merged in the order sent, except files the operations use, such as a watermark logo.
     *
     * Chain PendingPdf's methods to add operations, then call store(), file() or download(), or return it from a route.
     *
     * @param  list<string|MergeSource>  $sources  PDFs and images, in order. In a multipart request it may be left out: every uploaded PDF and image is merged in the order sent, except files the operations use.
     * @return PendingPdf
     */
    public function merge(array $sources): PendingPdf
    {
        return new PendingPdf(fn (array $body, string $accept) => $this->post('/pdf/merge', $body, $accept), [
            'sources' => $sources,
        ]);
    }

    /**
     * Split a PDF into parts saved in R2
     *
     * Splits a PDF into parts, every N pages ("every") or one per entry in "ranges", saves each to R2 and returns a signed link for each.
     *
     * @param  string|PdfSource  $source  A PDF: a PdfSourceObject or a shortcut string.
     * @param  list<string|list<int>>|null  $ranges  One part per entry, e.g. ["1-3", "4-last"].
     * @param  int|null  $every  Pages per part, when ranges is not given. Default: 1.
     * @param  string|null  $prefix  R2 key prefix for the parts. Default: "outputs/<uuid>/".
     * @param  int|null  $linkTtl  Lifetime of the signed download link, in seconds (max 604800 = 7 days). Default: the SIGNED_URL_TTL setting (3600).
     * @return SplitResponse
     *
     * @throws ApiException
     */
    public function split(
        string|PdfSource $source,
        ?array $ranges = null,
        ?int $every = null,
        ?string $prefix = null,
        ?int $linkTtl = null,
    ): SplitResponse {
        return SplitResponse::from($this->post('/pdf/split', [
            'source' => $source,
            'ranges' => $ranges,
            'every' => $every,
            'prefix' => $prefix,
            'linkTtl' => $linkTtl,
        ])->json());
    }

    /**
     * Width, height and line breaks of text in a font
     *
     * Measures text in a built-in font or a font file, optionally wrapped at a width, so text can be laid out before it is drawn. No PDF needed.
     *
     * @param  string  $text  Text to measure. "\n" always starts a new line.
     * @param  BuiltInFont|FontSource|null  $font  A built-in font name, or a font file. Text the font cannot draw is rejected with a 400 that names the characters. Default: "Helvetica".
     * @param  float|null  $size  Font size in points. Default: 12.
     * @param  float|null  $maxWidth  Wrap at this width, in points, and return the lines.
     * @param  list<string>|null  $wordBreaks  Characters after which a line may wrap. Default: [" "].
     * @param  float|null  $lineHeight  Distance between baselines. Default: 1.2 × size.
     * @param  float|null  $fitHeight  Also return the font size whose text height equals this.
     * @return MeasureResponse
     *
     * @throws ApiException
     */
    public function measureText(
        string $text,
        BuiltInFont|FontSource|null $font = null,
        ?float $size = null,
        ?float $maxWidth = null,
        ?array $wordBreaks = null,
        ?float $lineHeight = null,
        ?float $fitHeight = null,
    ): MeasureResponse {
        return MeasureResponse::from($this->post('/text/measure', [
            'text' => $text,
            'font' => $font,
            'size' => $size,
            'maxWidth' => $maxWidth,
            'wordBreaks' => $wordBreaks,
            'lineHeight' => $lineHeight,
            'fitHeight' => $fitHeight,
        ])->json());
    }
}
