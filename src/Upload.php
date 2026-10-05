<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers;

use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * A file sent along with a request. Any request holding one goes out as
 * multipart/form-data, with the JSON body in the "options" field.
 *
 * Usually created through a source's file(), contents() or disk() constructors.
 */
final readonly class Upload
{
    private function __construct(
        private ?string $path,
        private ?string $contents,
        public string $filename,
    ) {}

    /**
     * A file on the local filesystem, or one uploaded to your app ($request->file('…')).
     */
    public static function fromFile(SplFileInfo|string $file, ?string $filename = null): self
    {
        $path = $file instanceof SplFileInfo ? $file->getRealPath() : $file;

        if ($path === false || ! is_file($path) || ! is_readable($path)) {
            throw new InvalidArgumentException('Cannot read file: '.($file instanceof SplFileInfo ? $file->getPathname() : $file));
        }

        $filename ??= $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($path);

        return new self($path, null, $filename);
    }

    /**
     * Bytes you already have in memory.
     */
    public static function fromContents(string $contents, string $filename): self
    {
        return new self(null, $contents, $filename);
    }

    /**
     * A file on one of your Laravel filesystem disks.
     */
    public static function fromDisk(string $path, ?string $disk = null): self
    {
        $contents = Storage::disk($disk)->get($path);

        if ($contents === null) {
            throw new InvalidArgumentException("Cannot read [{$path}] from disk [".($disk ?? 'default').'].');
        }

        return new self(null, $contents, basename($path));
    }

    /**
     * The file's body: a fresh stream for files on disk, so an Upload can be sent more than once.
     *
     * @return string|resource
     */
    public function body(): mixed
    {
        if ($this->path === null) {
            return (string) $this->contents;
        }

        $stream = fopen($this->path, 'rb');

        if ($stream === false) {
            throw new InvalidArgumentException("Cannot open file: {$this->path}");
        }

        return $stream;
    }
}
