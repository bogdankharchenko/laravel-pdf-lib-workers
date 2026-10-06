<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Enums\AttachFileRelationship;
use BogdanKharchenko\PdfMill\Source;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Embeds a file inside the PDF.
 */
class AttachFile extends Data implements Operation
{
    /** Names this step in the operations list: always "attachFile". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string|Source  $file  A file (image, attachment, XML…): a FileSource object or a shortcut string.
     * @param  string  $name  File name shown in viewers.
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
