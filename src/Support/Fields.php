<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Support;

/**
 * @internal
 */
class Fields
{
    /**
     * Drops the options a source constructor was not given.
     *
     * @param  array<string, mixed>  $fields
     * @return array<string, mixed>
     */
    public static function compact(array $fields): array
    {
        return array_filter($fields, fn (mixed $value): bool => $value !== null);
    }
}
