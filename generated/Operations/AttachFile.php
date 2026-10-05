<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Enums\AttachFileRelationship;
use BogdanKharchenko\PdfLibWorkers\Source;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Embeds a file inside the PDF.
 */
final class AttachFile extends Data implements Operation
{
    /** Names this step in the operations list: always "attachFile". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string|Source  $file  A file (image, attachment, XML…): a FileSource object or a shortcut string.
     * @param  string  $name  File name shown in viewers.
     * @param  string|Optional  $mimeType
     * @param  string|Optional  $description
     * @param  string|Optional  $creationDate  A date, ideally ISO 8601.
     * @param  string|Optional  $modificationDate  A date, ideally ISO 8601.
     * @param  AttachFileRelationship|Optional  $relationship  How the file relates to the PDF (PDF/A-3 associated files).
     */
    public function __construct(
        public readonly string|Source $file,
        public readonly string $name,
        public readonly string|Optional $mimeType = new Optional(),
        public readonly string|Optional $description = new Optional(),
        public readonly string|Optional $creationDate = new Optional(),
        public readonly string|Optional $modificationDate = new Optional(),
        public readonly AttachFileRelationship|Optional $relationship = new Optional(),
    ) {
        $this->op = 'attachFile';
    }
}
