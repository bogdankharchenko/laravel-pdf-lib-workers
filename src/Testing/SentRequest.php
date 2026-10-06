<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Testing;

use BogdanKharchenko\PdfMill\Contracts\Operation;
use BogdanKharchenko\PdfMill\Support\Encoder;

/**
 * A request PdfMillFake answered: the endpoint called, and the options and
 * files sent, as the API would have read them.
 */
final readonly class SentRequest
{
    /**
     * @param  string  $endpoint  The method called, e.g. "edit" or "info".
     * @param  array<string, mixed>  $data  The options sent, decoded from JSON, e.g. ['source' => 'in.pdf', 'operations' => [...]].
     *                                      For download, the key: ['key' => 'outputs/abc.pdf'].
     * @param  array<string, array{filename: string|null, contents: string}>  $files  Uploaded files, by the name the options
     *                                                                                 refer to them by: {"upload": "file1"}.
     */
    public function __construct(
        public string $endpoint,
        public array $data,
        public array $files = [],
    ) {}

    /**
     * The operations sent to create, edit or merge, in order, e.g. [['op' => 'rotatePages', 'degrees' => 90]].
     *
     * @return list<array<string, mixed>>
     */
    public function operations(): array
    {
        return $this->data['operations'] ?? [];
    }

    /**
     * Whether an operation was sent with at least the given fields. Name the
     * operation and the fields to check, or build it as your code does:
     *
     *   $pdf->hasOperation('watermark', ['text' => 'DRAFT'])
     *   $pdf->hasOperation(new Watermark(text: 'DRAFT'))
     *
     * Fields left out match anything, in nested objects too; lists must match
     * in full. An upload matches a source object with the same filename and
     * contents, or the name it was sent under: ['upload' => 'file2'].
     *
     * @param  array<string, mixed>  $with  Fields it must have, beyond its name.
     */
    public function hasOperation(Operation|string $operation, array $with = []): bool
    {
        $encoder = new Encoder;
        $expected = [...$encoder->decoded(is_string($operation) ? ['op' => $operation] : $operation), ...$encoder->decoded($with)];
        $expectedFiles = [];
        foreach ($encoder->uploads() as ['name' => $name, 'upload' => $upload]) {
            $expectedFiles[$name] = ['filename' => $upload->filename, 'contents' => $upload->contents()];
        }
        $expected = self::withFiles($expected, $expectedFiles);
        $sentFiles = [];
        foreach ($this->files as $name => $file) {
            $sentFiles[$name] = ['name' => $name, ...$file];
        }

        foreach ($this->operations() as $sent) {
            if (self::matches(self::withFiles($sent, $sentFiles), $expected)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The value with each {"upload": "file1"} replaced by the file it names,
     * or by just its name: the numbering depends on the rest of the request,
     * so an expected source object is compared by its filename and contents.
     *
     * @param  array<string, array<string, string|null>>  $files
     */
    private static function withFiles(mixed $value, array $files): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        if (is_string($value['upload'] ?? null)) {
            $value['upload'] = $files[$value['upload']] ?? ['name' => $value['upload']];
        }

        return array_map(fn (mixed $item): mixed => self::withFiles($item, $files), $value);
    }

    /**
     * Whether $actual has every field of $expected: objects recursively, lists
     * item by item, and numbers by value, so 1 matches 1.0.
     */
    private static function matches(mixed $actual, mixed $expected): bool
    {
        if (! is_array($expected)) {
            return is_int($expected) || is_float($expected)
                ? (is_int($actual) || is_float($actual)) && $actual == $expected
                : $actual === $expected;
        }

        if (! is_array($actual) || (array_is_list($expected) && (! array_is_list($actual) || count($actual) !== count($expected)))) {
            return false;
        }

        foreach ($expected as $key => $value) {
            if (! array_key_exists($key, $actual) || ! self::matches($actual[$key], $value)) {
                return false;
            }
        }

        return true;
    }
}
