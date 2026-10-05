<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Support;

use BackedEnum;
use BogdanKharchenko\PdfLibWorkers\Contracts\Payload;
use BogdanKharchenko\PdfLibWorkers\Support\Transformers\PassThroughTransformer;
use BogdanKharchenko\PdfLibWorkers\Upload;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Transformation\TransformationContextFactory;
use stdClass;

/**
 * Turns a request's typed values into JSON-ready data, and collects the files
 * it refers to so they can be attached to a multipart request.
 *
 * laravel-data reads each data object's own fields (leaving Optional ones out
 * and keeping explicit nulls); nested objects come back here, so every JSON
 * object, empty or not, is encoded as one.
 *
 * @internal
 */
final class Encoder
{
    /** @var array<int, array{name: string, upload: Upload}> keyed by object id, in the order first seen */
    private array $uploads = [];

    private readonly TransformationContextFactory $fields;

    public function __construct()
    {
        $this->fields = TransformationContextFactory::create()
            ->withoutPropertyNameMapping() // the API's names, whatever the app's name_mapping_strategy
            ->withTransformer(Data::class, new PassThroughTransformer);
    }

    public function encode(mixed $value): mixed
    {
        return match (true) {
            $value instanceof Data => $this->object($value->transform($this->fields)),
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
     * @param  array<array-key, mixed>  $fields
     */
    private function object(array $fields): stdClass
    {
        return (object) array_map($this->encode(...), $fields);
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
