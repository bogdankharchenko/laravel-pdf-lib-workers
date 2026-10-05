<?php

/**
 * Generated from openapi.json (pdfmill 0.3.0, sha256 793485a9b985).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Operations;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Data\RadioChoice;
use BogdanKharchenko\PdfMill\Enums\AddFormFieldType;
use BogdanKharchenko\PdfMill\Enums\Alignment;
use BogdanKharchenko\PdfMill\Enums\BuiltInFont;
use BogdanKharchenko\PdfMill\Enums\Origin;
use BogdanKharchenko\PdfMill\FontSource;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Creates a form field. Text, checkbox, dropdown, optionList and button need page, x, y, width and height; radio needs choices.
 */
final class AddFormField extends Data implements Operation
{
    /** Names this step in the operations list: always "addFormField". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  AddFormFieldType  $type
     * @param  string  $name  Unique field name.
     * @param  int|Optional  $page  1-based page. Default: 1.
     * @param  float|Optional  $x
     * @param  float|Optional  $y  Bottom edge (bottom-left origin) or top edge (top-left origin).
     * @param  float|Optional  $width
     * @param  float|Optional  $height
     * @param  Origin|Optional  $origin  How to read x/y. "bottom-left": PDF coordinates in points, y measured up from the bottom edge. "top-left": y measured down from the top edge, like screen coordinates. Default: "bottom-left".
     * @param  string|bool|list<string>|Optional  $value  Starting value: text, checkbox true/false, the selected option(s).
     * @param  list<RadioChoice>|Optional  $choices  Radio groups: one entry per choice, each with its own box.
     * @param  string|Optional  $label  Button caption.
     * @param  BuiltInFont|FontSource|Optional  $font  A built-in font name, or a font file. Text the font cannot draw is rejected with a 400 that names the characters.
     * @param  string|Optional  $textColor  A hex colour: "#rrggbb" or "#rgb".
     * @param  string|Optional  $backgroundColor  A hex colour: "#rrggbb" or "#rgb".
     * @param  string|Optional  $borderColor  A hex colour: "#rrggbb" or "#rgb".
     * @param  float|Optional  $borderWidth  Default: 1 when borderColor is set.
     * @param  float|Optional  $rotate  Rotation in degrees, counter-clockwise.
     * @param  bool|Optional  $hidden
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
     */
    public function __construct(
        public readonly AddFormFieldType $type,
        public readonly string $name,
        public readonly int|Optional $page = new Optional(),
        public readonly float|Optional $x = new Optional(),
        public readonly float|Optional $y = new Optional(),
        public readonly float|Optional $width = new Optional(),
        public readonly float|Optional $height = new Optional(),
        public readonly Origin|Optional $origin = new Optional(),
        public readonly string|bool|array|Optional $value = new Optional(),
        public readonly array|Optional $choices = new Optional(),
        public readonly string|Optional $label = new Optional(),
        public readonly BuiltInFont|FontSource|Optional $font = new Optional(),
        public readonly string|Optional $textColor = new Optional(),
        public readonly string|Optional $backgroundColor = new Optional(),
        public readonly string|Optional $borderColor = new Optional(),
        public readonly float|Optional $borderWidth = new Optional(),
        public readonly float|Optional $rotate = new Optional(),
        public readonly bool|Optional $hidden = new Optional(),
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
    ) {
        $this->op = 'addFormField';
    }
}
