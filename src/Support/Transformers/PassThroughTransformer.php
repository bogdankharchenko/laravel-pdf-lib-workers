<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Support\Transformers;

use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

/**
 * Leaves a value as it is, so the Encoder can encode it itself.
 *
 * @internal
 */
final class PassThroughTransformer implements Transformer
{
    public function transform(DataProperty $property, mixed $value, TransformationContext $context): mixed
    {
        return $value;
    }
}
