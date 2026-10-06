<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill;

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
readonly class Upload
{
    /**
     * @param  SplFileInfo|null  $file  The file object itself, not just its path: that keeps a
     *                                  temporary file, such as UploadedFile::fake(), until it is sent.
     */
    private function __construct(
        private ?SplFileInfo $file,
        private ?string $contents,
        public string $filename,
    ) {}

    /**
     * A file on the local filesystem, or one uploaded to your app ($request->file('…')).
     */
    public static function fromFile(SplFileInfo|string $file, ?string $filename = null): self
    {
        $file = $file instanceof SplFileInfo ? $file : new SplFileInfo($file);

        if (! $file->isFile() || ! $file->isReadable()) {
            throw new InvalidArgumentException('Cannot read file: '.$file->getPathname());
        }

        $filename ??= $file instanceof UploadedFile ? $file->getClientOriginalName() : $file->getFilename();

        return new self($file, null, $filename);
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
     * The file's bytes.
     */
    public function contents(): string
    {
        $body = $this->body();
        if (is_string($body)) {
            return $body;
        }

        try {
            return (string) stream_get_contents($body);
        } finally {
            fclose($body);
        }
    }

    /**
     * The file's body: a fresh stream for files on disk, so an Upload can be sent more than once.
     *
     * @return string|resource
     */
    public function body(): mixed
    {
        if ($this->file === null) {
            return (string) $this->contents;
        }

        $stream = fopen($this->file->getPathname(), 'rb');

        if ($stream === false) {
            throw new InvalidArgumentException('Cannot open file: '.$this->file->getPathname());
        }

        return $stream;
    }
}
