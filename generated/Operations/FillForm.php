<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Enums\Alignment;
use BogdanKharchenko\PdfLibWorkers\Enums\BuiltInFont;
use BogdanKharchenko\PdfLibWorkers\FontSource;
use BogdanKharchenko\PdfLibWorkers\Source;
use BogdanKharchenko\PdfLibWorkers\Support\Transformers\JsonObjectTransformer;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Fills form fields by name (see /pdf/info for names and types).
 */
final class FillForm extends Data implements Operation
{
    /** Names this step in the operations list: always "fillForm". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  array<array-key, string|bool|list<string>>|Optional  $fields  Text fields take a string; checkboxes true/false; dropdowns and option lists an option or array of options; radio groups an option. Default: [].
     * @param  array<array-key, string|Source>|Optional  $images  Images for text fields or buttons, by field name (e.g. a signature box).
     * @param  Alignment|Optional  $imageAlignment  Horizontal alignment.
     * @param  bool|Optional  $flatten  Turn the fields into plain page content afterwards, so they can no longer be edited. Default: false.
     * @param  bool|Optional  $strict  Fail on unknown field names instead of ignoring them. Default: true.
     * @param  BuiltInFont|FontSource|Optional  $font  Font for the filled-in values. Default: Helvetica. Use a font file for non-Latin text.
     */
    public function __construct(
        #[WithTransformer(JsonObjectTransformer::class)]
        public readonly array|Optional $fields = new Optional(),
        #[WithTransformer(JsonObjectTransformer::class)]
        public readonly array|Optional $images = new Optional(),
        public readonly Alignment|Optional $imageAlignment = new Optional(),
        public readonly bool|Optional $flatten = new Optional(),
        public readonly bool|Optional $strict = new Optional(),
        public readonly BuiltInFont|FontSource|Optional $font = new Optional(),
    ) {
        $this->op = 'fillForm';
    }
}
