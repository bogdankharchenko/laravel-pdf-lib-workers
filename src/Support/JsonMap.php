<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Support;

/**
 * Marks an array as a JSON object, so it is sent as {} when empty and keeps
 * keys that look like numbers ("0", "2026") as keys.
 *
 * @internal
 */
readonly class JsonMap
{
    /**
     * @param  array<array-key, mixed>  $values
     */
    public function __construct(public array $values) {}
}
