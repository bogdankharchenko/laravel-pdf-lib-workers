<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

/**
 * An encrypted PDF sent without its password: only its structure is readable.
 */
final class LockedInfoResponse extends Data
{
    /**
     * @param  int  $pageCount
     * @param  true  $encrypted
     * @param  true  $needsPassword
     * @param  list<PageInfo>  $pages
     */
    public function __construct(
        public readonly int $pageCount,
        public readonly bool $encrypted,
        public readonly bool $needsPassword,
        #[DataCollectionOf(PageInfo::class)]
        public readonly array $pages,
    ) {
    }
}
