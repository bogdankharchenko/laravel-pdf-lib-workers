<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Enums\Alignment;
use BogdanKharchenko\PdfMill\Enums\BuiltInFont;
use BogdanKharchenko\PdfMill\FontSource;
use BogdanKharchenko\PdfMill\Source;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Changes a field's settings, or shows an image in it.
 */
final class SetFieldProperties extends Data implements Operation
{
    /** Names this step in the operations list: always "setFieldProperties". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  string  $name
     * @param  bool|Optional  $readOnly
     * @param  bool|Optional  $required
     * @param  bool|Optional  $exported  false keeps the field's value out of form submissions.
     * @param  bool|Optional  $multiline  Text fields.
     * @param  int|Optional|null  $maxLength  Text fields: maximum characters; null removes the limit. Leave out to not send it; null is sent as null.
     * @param  Alignment|Optional  $alignment  Text fields.
     * @param  float|Optional  $fontSize  Text fields, dropdowns, option lists and buttons. 0 = auto-size.
     * @param  bool|Optional  $password  Text fields: hide the characters typed.
     * @param  bool|Optional  $comb  Text fields: one character per box across the field's width (needs maxLength).
     * @param  bool|Optional  $spellCheck  Text fields and dropdowns.
     * @param  bool|Optional  $scroll  Text fields: allow text longer than the box.
     * @param  bool|Optional  $richText  Text fields.
     * @param  bool|Optional  $fileSelect  Text fields: the value is a file path.
     * @param  list<string>|Optional  $options  Dropdowns and option lists: the choices.
     * @param  bool|Optional  $editable  Dropdowns: allow typing a value not in the list.
     * @param  bool|Optional  $sort  Dropdowns and option lists.
     * @param  bool|Optional  $multiselect  Dropdowns and option lists.
     * @param  bool|Optional  $selectOnClick  Dropdowns and option lists: commit the choice as soon as it is clicked.
     * @param  bool|Optional  $offToggle  Radio groups: clicking the selected option clears it.
     * @param  bool|Optional  $mutuallyExclusive  Radio groups, addFormField only: true (default) turns on one button at a time; false turns on every button sharing the chosen value.
     * @param  string|Source|Optional  $image  Text fields and buttons: an image to show in the field.
     * @param  Alignment|Optional  $imageAlignment  Horizontal alignment.
     * @param  BuiltInFont|FontSource|Optional  $font  Font used to redraw the field. Default: Helvetica.
     */
    public function __construct(
        public readonly string $name,
        public readonly bool|Optional $readOnly = new Optional(),
        public readonly bool|Optional $required = new Optional(),
        public readonly bool|Optional $exported = new Optional(),
        public readonly bool|Optional $multiline = new Optional(),
        public readonly int|Optional|null $maxLength = new Optional(),
        public readonly Alignment|Optional $alignment = new Optional(),
        public readonly float|Optional $fontSize = new Optional(),
        public readonly bool|Optional $password = new Optional(),
        public readonly bool|Optional $comb = new Optional(),
        public readonly bool|Optional $spellCheck = new Optional(),
        public readonly bool|Optional $scroll = new Optional(),
        public readonly bool|Optional $richText = new Optional(),
        public readonly bool|Optional $fileSelect = new Optional(),
        public readonly array|Optional $options = new Optional(),
        public readonly bool|Optional $editable = new Optional(),
        public readonly bool|Optional $sort = new Optional(),
        public readonly bool|Optional $multiselect = new Optional(),
        public readonly bool|Optional $selectOnClick = new Optional(),
        public readonly bool|Optional $offToggle = new Optional(),
        public readonly bool|Optional $mutuallyExclusive = new Optional(),
        public readonly string|Source|Optional $image = new Optional(),
        public readonly Alignment|Optional $imageAlignment = new Optional(),
        public readonly BuiltInFont|FontSource|Optional $font = new Optional(),
    ) {
        $this->op = 'setFieldProperties';
    }
}
