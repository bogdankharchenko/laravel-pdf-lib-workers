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
use BogdanKharchenko\PdfMill\Testing\PdfMillFake;
use BogdanKharchenko\PdfMill\Testing\SentRequest;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * The pdfmill API. In tests, PdfMill::fake() stands in for it and adds the assert methods.
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
 * @method static void assertSent(string|Closure $endpoint, Closure|int|null $callback = null)
 * @method static void assertNotSent(string|Closure $endpoint, Closure|null $callback = null)
 * @method static void assertSentCount(int $count)
 * @method static void assertNothingSent()
 * @method static Collection<int, SentRequest> sent(string|Closure|null $endpoint = null, Closure|null $callback = null)
 *
 * @phpstan-import-type Replies from PdfMillFake
 *
 * @see Client
 * @see PdfMillFake
 */
final class PdfMill extends Facade
{
    /**
     * Stands in for the API in a test: nothing is sent, each endpoint answers
     * as the API would, and every request is recorded for the assert methods.
     *
     * @param  Replies  $replies  Replies by endpoint name, e.g. "merge" or "info"; see PdfMillFake.
     */
    public static function fake(array $replies = []): PdfMillFake
    {
        static::swap($fake = new PdfMillFake($replies));

        return $fake;
    }

    protected static function getFacadeAccessor(): string
    {
        return Client::class;
    }
}
