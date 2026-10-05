<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Tests;

use BogdanKharchenko\PdfLibWorkers\Facades\PdfLib;
use BogdanKharchenko\PdfLibWorkers\MergeSource;
use BogdanKharchenko\PdfLibWorkers\PdfSource;
use BogdanKharchenko\PdfLibWorkers\Source;
use BogdanKharchenko\PdfLibWorkers\Upload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;

/**
 * Requests that carry files go out as multipart, with the JSON body in "options".
 */
final class UploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->fakeEndpoints();
    }

    public function test_sends_files_as_parts_and_refers_to_them_by_name(): void
    {
        PdfLib::edit(PdfSource::contents('%PDF-in', 'in.pdf', password: 'secret'))
            ->fillForm(images: ['signature' => Source::contents('PNG-sig', 'sig.png')])
            ->store();

        $request = $this->sentRequest();
        $this->assertSame('https://pdf.test/pdf/edit', $request->url());
        $this->assertSame(['Bearer test-key'], $request->header('Authorization'));
        $this->assertSame(['application/json'], $request->header('Accept'));

        $parts = $this->sentParts();
        $this->assertSame(['options', 'file1', 'file2'], array_keys($parts));
        $this->assertSame(['contents' => '%PDF-in', 'filename' => 'in.pdf'], $parts['file1']);
        $this->assertSame(['contents' => 'PNG-sig', 'filename' => 'sig.png'], $parts['file2']);
        $this->assertJsonIs(<<<'JSON'
            {
                "source": {"upload": "file1", "password": "secret"},
                "operations": [{"op": "fillForm", "images": {"signature": {"upload": "file2"}}}]
            }
            JSON, $parts['options']['contents']);
    }

    public function test_sends_a_source_used_twice_once(): void
    {
        $logo = Source::contents('PNG-logo', 'logo.png');

        PdfLib::edit('in.pdf')->drawImage($logo, 10, 10)->drawImage($logo, 500, 10)->store();

        $parts = $this->sentParts();
        $this->assertSame(['options', 'file1'], array_keys($parts));
        $this->assertJsonIs(<<<'JSON'
            {
                "source": "in.pdf",
                "operations": [
                    {"op": "drawImage", "image": {"upload": "file1"}, "x": 10, "y": 10},
                    {"op": "drawImage", "image": {"upload": "file1"}, "x": 500, "y": 10}
                ]
            }
            JSON, $parts['options']['contents']);
    }

    public function test_sends_each_source_made_from_a_file_separately(): void
    {
        $path = $this->tempFile('%PDF-a');

        PdfLib::merge([MergeSource::file($path), MergeSource::file($path, 'again.pdf', pages: 'last')])->store();

        $parts = $this->sentParts();
        $this->assertSame(['contents' => '%PDF-a', 'filename' => basename($path)], $parts['file1']);
        $this->assertSame(['contents' => '%PDF-a', 'filename' => 'again.pdf'], $parts['file2']);
        $this->assertJsonIs('{"sources": [{"upload": "file1"}, {"upload": "file2", "pages": "last"}]}', $parts['options']['contents']);
    }

    public function test_names_an_uploaded_file_as_its_user_did(): void
    {
        $upload = UploadedFile::fake()->createWithContent('Signed contract.pdf', '%PDF-signed');

        PdfLib::info(PdfSource::file($upload));

        $this->assertSame(['contents' => '%PDF-signed', 'filename' => 'Signed contract.pdf'], $this->sentParts()['file1']);
    }

    public function test_keeps_a_temporary_file_until_it_is_sent(): void
    {
        // A fake upload's file is deleted once nothing refers to the UploadedFile.
        PdfLib::info(PdfSource::file(UploadedFile::fake()->createWithContent('scan.pdf', '%PDF-scan')));

        $this->assertSame(['contents' => '%PDF-scan', 'filename' => 'scan.pdf'], $this->sentParts()['file1']);
    }

    public function test_reads_files_from_a_disk(): void
    {
        Storage::fake('templates');
        Storage::disk('templates')->put('forms/w9.pdf', '%PDF-w9');

        PdfLib::info(PdfSource::disk('forms/w9.pdf', 'templates'));

        $this->assertSame(['contents' => '%PDF-w9', 'filename' => 'w9.pdf'], $this->sentParts()['file1']);
    }

    public function test_rejects_a_file_it_cannot_read(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot read file: /no/such/file.pdf');

        Upload::fromFile('/no/such/file.pdf');
    }

    public function test_rejects_a_missing_disk_file(): void
    {
        Storage::fake('templates');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot read [missing.pdf] from disk [templates].');

        Upload::fromDisk('missing.pdf', 'templates');
    }

    private function tempFile(string $contents): string
    {
        $path = tempnam(sys_get_temp_dir(), 'pdf-lib-workers-');
        $this->assertIsString($path);
        file_put_contents($path, $contents);
        $this->beforeApplicationDestroyed(function () use ($path): void {
            unlink($path);
        });

        return $path;
    }
}
