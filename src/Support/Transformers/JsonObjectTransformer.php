<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Support\Transformers;

use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;
use stdClass;

/**
 * Sends a map (string keys) as a JSON object, so an empty one is {} rather than [].
 *
 * @internal
 */
final class JsonObjectTransformer implements Transformer
{
    public function transform(DataProperty $property, mixed $value, TransformationContext $context): mixed
    {
        return $value === [] ? new stdClass : $value;
    }
}
