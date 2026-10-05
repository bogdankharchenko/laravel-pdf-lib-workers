<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Support\Transformers;

use BogdanKharchenko\PdfMill\Support\JsonMap;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

/**
 * Marks a map (string keys) so it is sent as a JSON object: {} when empty,
 * and {"0": …} rather than a list when its keys look like numbers.
 *
 * @internal
 */
final class JsonObjectTransformer implements Transformer
{
    public function transform(DataProperty $property, mixed $value, TransformationContext $context): mixed
    {
        return is_array($value) ? new JsonMap($value) : $value;
    }
}
