<?php

/**
 * Generated from openapi.json (pdfmill 0.4.0, sha256 caa72f5358df).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Data;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * What to do with the PDF this request produces. Send Accept: application/pdf to get the PDF bytes instead of a JSON PdfResult.
 */
class Output extends Data
{
    /**
     * @param  string|Optional  $key  Where to save in R2; overwrites an existing file. Default: "outputs/<uuid>.pdf", which the recommended expiry rule deletes after 7 days. Keys outside outputs/ and extracted/ are kept.
     * @param  string|Optional  $filename  Name offered when the PDF is opened or saved. Default: "document.pdf".
     * @param  bool|Optional  $store  Save to R2. With false, a JSON response carries the PDF as base64. Ignored with put. Default: true.
     * @param  int|Optional  $linkTtl  Lifetime of the signed download link, in seconds (max 604800 = 7 days). Default: the SIGNED_URL_TTL setting (3600).
     * @param  PutTarget|Optional  $put  Upload the PDF to this URL instead of saving it to R2. The reply is an UploadedPdf, never the PDF itself.
     * @param  bool|Optional  $useObjectStreams  Compress objects into streams (smaller files). false writes a classic cross-reference table for old tools. Default: true.
     */
    public function __construct(
        public readonly string|Optional $key = new Optional(),
        public readonly string|Optional $filename = new Optional(),
        public readonly bool|Optional $store = new Optional(),
        public readonly int|Optional $linkTtl = new Optional(),
        public readonly PutTarget|Optional $put = new Optional(),
        public readonly bool|Optional $useObjectStreams = new Optional(),
    ) {
    }
}
