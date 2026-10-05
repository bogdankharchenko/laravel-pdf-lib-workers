<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.2, sha256 dcb53f93ae8a).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Operations;

use BogdanKharchenko\PdfLibWorkers\Contracts\Operation;
use BogdanKharchenko\PdfLibWorkers\Enums\ConvertToPDFAConformance;
use BogdanKharchenko\PdfLibWorkers\Source;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

/**
 * Adds what PDF/A requires (sRGB output intent, file ID, XMP). Text must use an embedded font file, and the PDF must not be encrypted.
 */
final class ConvertToPDFA extends Data implements Operation
{
    /** Names this step in the operations list: always "convertToPDFA". */
    #[Computed]
    public readonly string $op;

    /**
     * @param  ConvertToPDFAConformance|Optional  $conformance  Default: "3B".
     * @param  string|Source|Optional  $iccProfile  ICC colour profile. Default: sRGB.
     * @param  string|Optional  $outputConditionIdentifier
     * @param  1|3|4|Optional  $colorComponents  Components of the ICC profile: 1 gray, 3 RGB, 4 CMYK.
     */
    public function __construct(
        public readonly ConvertToPDFAConformance|Optional $conformance = new Optional(),
        public readonly string|Source|Optional $iccProfile = new Optional(),
        public readonly string|Optional $outputConditionIdentifier = new Optional(),
        public readonly int|Optional $colorComponents = new Optional(),
    ) {
        $this->op = 'convertToPDFA';
    }
}
