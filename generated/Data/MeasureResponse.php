<?php

/**
 * Generated from openapi.json (pdf-lib-workers 0.2.1, sha256 afab6278fb6f).
 * Do not edit: change the API's spec, copy it here and run `composer generate`.
 */

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;

final class MeasureResponse extends Data
{
    /**
     * @param  float  $width  Width of the widest line, in points.
     * @param  float  $height  Height of one line of text, including descenders.
     * @param  float  $ascent  Height above the baseline.
     * @param  list<MeasuredLine>  $lines
     * @param  float  $blockHeight  Height of all lines, using lineHeight between baselines.
     * @param  float|null  $sizeForHeight  With fitHeight: the font size whose text height equals it.
     */
    public function __construct(
        public readonly float $width,
        public readonly float $height,
        public readonly float $ascent,
        #[DataCollectionOf(MeasuredLine::class)]
        public readonly array $lines,
        public readonly float $blockHeight,
        public readonly ?float $sizeForHeight = null,
    ) {
    }
}
