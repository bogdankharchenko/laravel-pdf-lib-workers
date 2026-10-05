<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Support;

use BackedEnum;
use BogdanKharchenko\PdfLibWorkers\Contracts\Payload;
use BogdanKharchenko\PdfLibWorkers\Upload;
use Spatie\LaravelData\Data;
use stdClass;

/**
 * Turns a request's typed values into JSON-ready data, and collects the files
 * it refers to so they can be attached to a multipart request.
 *
 * laravel-data objects go through their own toArray() (which leaves Optional
 * fields out and keeps explicit nulls); sources and uploads are handled here.
 *
 * @internal
 */
final class Encoder
{
    /** @var array<int, array{name: string, upload: Upload}> keyed by object id, in the order first seen */
    private array $uploads = [];

    public function encode(mixed $value): mixed
    {
        return match (true) {
            $value instanceof Data => $this->object($value->toArray()),
            $value instanceof Payload => $this->object($value->payload()),
            $value instanceof JsonMap => $this->object($value->values),
            $value instanceof BackedEnum => $value->value,
            $value instanceof Upload => $this->register($value),
            is_array($value) => array_map($this->encode(...), $value),
            default => $value,
        };
    }

    /**
     * Files referred to by the encoded value, with the field names they are sent under.
     *
     * @return list<array{name: string, upload: Upload}>
     */
    public function uploads(): array
    {
        return array_values($this->uploads);
    }

    /**
     * @param  array<string, mixed>  $fields
     * @return array<string, mixed>|stdClass
     */
    private function object(array $fields): array|stdClass
    {
        $encoded = array_map($this->encode(...), $fields);

        return $encoded === [] ? new stdClass : $encoded;
    }

    /**
     * The same Upload used twice is sent once.
     */
    private function register(Upload $upload): string
    {
        $id = spl_object_id($upload);
        $this->uploads[$id] ??= ['name' => 'file'.(count($this->uploads) + 1), 'upload' => $upload];

        return $this->uploads[$id]['name'];
    }
}
