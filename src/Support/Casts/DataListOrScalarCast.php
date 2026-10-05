<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Support\Casts;

use Spatie\LaravelData\Casts\Cast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Creation\CreationContext;
use Spatie\LaravelData\Support\DataProperty;

/**
 * Reads a field that is either a list of objects or a plain value, e.g.
 * list<FieldError>|string: arrays become the data class, anything else is kept.
 *
 * @internal
 */
final readonly class DataListOrScalarCast implements Cast
{
    /**
     * @param  class-string<Data>  $dataClass
     */
    public function __construct(private string $dataClass) {}

    /**
     * @param  array<string, mixed>  $properties
     * @param  CreationContext<Data>  $context
     */
    public function cast(DataProperty $property, mixed $value, array $properties, CreationContext $context): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        return array_values(array_map(fn (mixed $item): Data => $this->dataClass::from($item), $value));
    }
}
