<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use BogdanKharchenko\PdfMill\Support\Transformers\JsonObjectTransformer;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Where to upload the PDF, instead of saving it to R2.
 */
class PutTarget extends Data
{
    /**
     * @param  string  $url  http(s) URL the Worker PUTs the PDF to, such as an S3 presigned upload URL.
     * @param  array<array-key, string>|Optional  $headers  Extra request headers, e.g. ones the URL was signed with. Content-Type is "application/pdf" unless set here; Host and Content-Length come from the URL and the file.
     */
    public function __construct(
        public readonly string $url,
        #[WithTransformer(JsonObjectTransformer::class)]
        public readonly array|Optional $headers = new Optional(),
    ) {
    }
}
