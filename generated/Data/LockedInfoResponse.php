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
 * An encrypted PDF sent without its password: only its structure is readable.
 */
class LockedInfoResponse extends Data
{
    /**
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
