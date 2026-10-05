<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Support;

/**
 * Marks an array as a JSON object (string keys), so an empty one is sent as {} rather than [].
 *
 * @internal
 */
final readonly class JsonMap
{
    /**
     * @param  array<string, mixed>  $values
     */
    public function __construct(public array $values) {}
}
