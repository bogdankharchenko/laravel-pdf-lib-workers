<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Data;

final class SplitPart extends Data
{
    /**
     * @param  string  $key  R2 key of the saved file.
     * @param  string  $url  Signed download link; works without the API key until expiresAt.
     * @param  string  $expiresAt  When the link stops working (ISO 8601).
     * @param  list<int>  $pages  1-based source pages in this part.
     * @param  int  $size
     */
    public function __construct(
        public readonly string $key,
        public readonly string $url,
        public readonly string $expiresAt,
        public readonly array $pages,
        public readonly int $size,
    ) {
    }
}
