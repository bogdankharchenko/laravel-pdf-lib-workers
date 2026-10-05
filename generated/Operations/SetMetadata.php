<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Support\Transformers\JsonObjectTransformer;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Sets document properties, copyright and custom fields, in both the Info dictionary and XMP. Run it before convertToPDFA.
 */
final class SetMetadata extends Data implements Operation
{
    /** Names this step in the operations list: always "setMetadata". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string|Optional  $title
     * @param  bool|Optional  $showTitleInWindow  Show the title instead of the file name in viewers' title bar.
     * @param  string|Optional  $author
     * @param  string|Optional  $subject
     * @param  list<string>|Optional  $keywords
     * @param  string|Optional  $creator  The application that made the original content.
     * @param  string|Optional  $producer  The application that made the PDF.
     * @param  string|Optional  $language  Language tag, e.g. "en-GB".
     * @param  string|Optional  $creationDate  A date, ideally ISO 8601.
     * @param  string|Optional  $modificationDate  Default: now.
     * @param  string|Optional  $copyright  e.g. "© 2026 Acme Inc. All rights reserved." Shown as Acrobat's copyright notice.
     * @param  string|Optional  $copyrightUrl  Page with licence or ownership details.
     * @param  array<string, string|null>|Optional  $custom  Your own fields, e.g. { "MadeFor": "Client X" }. Keys are letters, digits and _ (max 64). null removes a field.
     */
    public function __construct(
        public readonly string|Optional $title = new Optional(),
        public readonly bool|Optional $showTitleInWindow = new Optional(),
        public readonly string|Optional $author = new Optional(),
        public readonly string|Optional $subject = new Optional(),
        public readonly array|Optional $keywords = new Optional(),
        public readonly string|Optional $creator = new Optional(),
        public readonly string|Optional $producer = new Optional(),
        public readonly string|Optional $language = new Optional(),
        public readonly string|Optional $creationDate = new Optional(),
        public readonly string|Optional $modificationDate = new Optional(),
        public readonly string|Optional $copyright = new Optional(),
        public readonly string|Optional $copyrightUrl = new Optional(),
        #[WithTransformer(JsonObjectTransformer::class)]
        public readonly array|Optional $custom = new Optional(),
    ) {
        $this->op = 'setMetadata';
    }
}
