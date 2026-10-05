<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Facades;

use BogdanKharchenko\PdfLibWorkers\Client;
use BogdanKharchenko\PdfLibWorkers\Data\ExtractResponse;
use BogdanKharchenko\PdfLibWorkers\Data\InfoResponse;
use BogdanKharchenko\PdfLibWorkers\Data\LockedInfoResponse;
use BogdanKharchenko\PdfLibWorkers\Data\MeasureResponse;
use BogdanKharchenko\PdfLibWorkers\Data\ScriptsResponse;
use BogdanKharchenko\PdfLibWorkers\Data\SplitResponse;
use BogdanKharchenko\PdfLibWorkers\Data\TextResponse;
use BogdanKharchenko\PdfLibWorkers\Enums\BuiltInFont;
use BogdanKharchenko\PdfLibWorkers\Enums\ExtractInclude;
use BogdanKharchenko\PdfLibWorkers\Enums\PaperSize;
use BogdanKharchenko\PdfLibWorkers\FileResponse;
use BogdanKharchenko\PdfLibWorkers\FontSource;
use BogdanKharchenko\PdfLibWorkers\MergeSource;
use BogdanKharchenko\PdfLibWorkers\PdfSource;
use BogdanKharchenko\PdfLibWorkers\PendingPdf;
use Illuminate\Support\Facades\Facade;

/**
 * The pdf-lib-workers API.
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
final class PdfLib extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Client::class;
    }
}
