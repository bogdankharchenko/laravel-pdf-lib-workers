<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Facades;

use BogdanKharchenko\PdfMill\Client;
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
use BogdanKharchenko\PdfMill\FileResponse;
use BogdanKharchenko\PdfMill\FontSource;
use BogdanKharchenko\PdfMill\MergeSource;
use BogdanKharchenko\PdfMill\PdfSource;
use BogdanKharchenko\PdfMill\PendingPdf;
use Illuminate\Support\Facades\Facade;

/**
 * The pdfmill API.
 *
 * @method static FileResponse download(string $key)
 * @method static InfoResponse|LockedInfoResponse info(string|PdfSource $source)
 * @method static TextResponse text(string|PdfSource $source, string|list<int>|null $pages = null, bool|null $items = null)
 * @method static ExtractResponse extract(string|PdfSource $source, string|list<int>|null $pages = null, list<ExtractInclude>|null $include = null, bool|null $store = null, string|null $prefix = null, int|null $linkTtl = null)
 * @method static ScriptsResponse scripts(string|PdfSource $source)
 * @method static PendingPdf create(PaperSize|array{float, float}|null $size = null, int|null $pageCount = null)
 * @method static PendingPdf edit(string|PdfSource $source, bool|null $incremental = null)
 * @method static PendingPdf merge(list<string|MergeSource> $sources)
 * @method static SplitResponse split(string|PdfSource $source, list<string|list<int>>|null $ranges = null, int|null $every = null, string|null $prefix = null, int|null $linkTtl = null)
 * @method static MeasureResponse measureText(string $text, BuiltInFont|FontSource|null $font = null, float|null $size = null, float|null $maxWidth = null, list<string>|null $wordBreaks = null, float|null $lineHeight = null, float|null $fitHeight = null)
 *
 * @see Client
 */
final class PdfMill extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Client::class;
    }
}
