<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Contracts;

/**
 * A source (Source, PdfSource, MergeSource, FontSource): sent as a JSON object.
 * Everything else sent is a laravel-data object.
 */
interface Payload
{
    /**
     * The fields to send, keyed by their JSON names. Values may be other
     * payloads, enums, uploads or maps; the Encoder turns them into JSON.
     *
     * @return array<string, mixed>
     */
    public function payload(): array;
}
