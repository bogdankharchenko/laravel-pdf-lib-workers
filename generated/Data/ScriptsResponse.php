<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

final class ScriptsResponse extends Data
{
    /**
     * @param  list<DocumentScript>  $document
     * @param  list<FieldScript>  $fields
     * @param  list<PageScript>  $pages
     * @param  list<XfaScript>  $xfa
     */
    public function __construct(
        #[DataCollectionOf(DocumentScript::class)]
        public readonly array $document,
        #[DataCollectionOf(FieldScript::class)]
        public readonly array $fields,
        #[DataCollectionOf(PageScript::class)]
        public readonly array $pages,
        #[DataCollectionOf(XfaScript::class)]
        public readonly array $xfa,
    ) {
    }
}
