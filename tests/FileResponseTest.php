<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Tests;

use BogdanKharchenko\PdfMill\Exceptions\NotFoundException;
use BogdanKharchenko\PdfMill\Facades\PdfMill;
use BogdanKharchenko\PdfMill\FileResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * PendingPdf::file() and download(): the file itself, not JSON.
 */
final class FileResponseTest extends TestCase
{
    private const PDF = "%PDF-1.7\n…";

    public function test_asks_for_the_pdf_and_reads_its_headers(): void
    {
        Http::fake(['*' => Http::response(self::PDF, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Invoice 42.pdf"',
            'X-Page-Count' => '3',
            'X-File-Key' => 'invoices/42.pdf',
            'X-File-Url' => 'https://pdf.test/files/invoices/42.pdf?expires=1&sig=x',
        ])]);

        $file = PdfMill::edit('in.pdf')->rotatePages(90)->file(storeAs: 'invoices/42.pdf');

        $request = $this->sentRequest();
        $this->assertSame('https://pdf.test/pdf/edit', $request->url());
        $this->assertSame(['application/pdf'], $request->header('Accept'));
        $this->assertSentJson('{"source": "in.pdf", "operations": [{"op": "rotatePages", "degrees": 90}], "output": {"key": "invoices/42.pdf"}}');

        $this->assertSame(self::PDF, $file->contents);
        $this->assertSame(strlen(self::PDF), $file->size());
        $this->assertSame('application/pdf', $file->contentType);
        $this->assertSame('Invoice 42.pdf', $file->filename);
        $this->assertSame(3, $file->pageCount);
        $this->assertSame('invoices/42.pdf', $file->key);
        $this->assertSame('https://pdf.test/files/invoices/42.pdf?expires=1&sig=x', $file->url);
    }

    public function test_does_not_keep_the_pdf_unless_asked(): void
    {
        Http::fake(['*' => Http::response(self::PDF, 200, ['Content-Type' => 'application/pdf', 'X-Page-Count' => '1'])]);

        $file = PdfMill::create()->file();

        $this->assertSentJson('{"output": {"store": false}}');

        $this->assertSame('document.pdf', $file->filename);
        $this->assertSame(1, $file->pageCount);
        $this->assertNull($file->key);
        $this->assertNull($file->url);
    }

    public function test_reads_a_non_ascii_name_from_filename_star(): void
    {
        Http::fake(['*' => Http::response(self::PDF, 200, [
            'Content-Disposition' => "inline; filename=\"_____ 2026.pdf\"; filename*=UTF-8''%D0%9E%D1%82%D1%87%D1%91%D1%82%202026.pdf",
        ])]);

        $this->assertSame('Отчёт 2026.pdf', PdfMill::create()->file()->filename);
    }

    public function test_keeps_only_the_base_of_a_name(): void
    {
        Http::fake(['*' => Http::response(self::PDF, 200, ['Content-Disposition' => 'attachment; filename="../../etc/passwd"'])]);

        $this->assertSame('passwd', PdfMill::create()->file()->filename);
    }

    public function test_downloads_by_key_with_each_segment_encoded(): void
    {
        Http::fake(['*' => Http::response('a,b', 200, ['Content-Type' => 'text/csv'])]);

        $file = PdfMill::download('extracted/run 1/data #1?.csv');

        $request = $this->sentRequest();
        $this->assertSame('GET', $request->method());
        $this->assertSame('https://pdf.test/files/extracted/run%201/data%20%231%3F.csv', $request->url());
        $this->assertSame(['Bearer test-key'], $request->header('Authorization'));
        $this->assertSame(['*/*'], $request->header('Accept'));

        $this->assertSame('a,b', $file->contents);
        $this->assertSame('text/csv', $file->contentType);
        $this->assertSame('data #1?.csv', $file->filename);
        $this->assertNull($file->pageCount);
    }

    public function test_download_throws_for_a_missing_file(): void
    {
        $this->respondWith('error-404');

        $this->expectException(NotFoundException::class);

        PdfMill::download('missing/file.pdf');
    }

    public function test_shows_the_file_in_the_browser(): void
    {
        $response = $this->file('Отчёт.pdf')->toResponse(new Request);

        $this->assertSame(self::PDF, $response->getContent());
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertSame((string) strlen(self::PDF), $response->headers->get('Content-Length'));
        $this->assertSame("inline; filename=_____.pdf; filename*=utf-8''%D0%9E%D1%82%D1%87%D1%91%D1%82.pdf", $response->headers->get('Content-Disposition'));
    }

    public function test_makes_the_browser_save_the_file(): void
    {
        $file = $this->file('report.pdf');

        $this->assertSame('attachment; filename=report.pdf', $file->download()->headers->get('Content-Disposition'));
        $this->assertSame('attachment; filename="Q3 report.pdf"', $file->download('Q3 report.pdf')->headers->get('Content-Disposition'));
    }

    public function test_saves_the_file_to_a_disk(): void
    {
        $disk = Storage::fake('reports');

        $path = $this->file('report.pdf')->save('2026/q3.pdf', 'reports');

        $this->assertSame('2026/q3.pdf', $path);
        $disk->assertExists('2026/q3.pdf', self::PDF);
    }

    private function file(string $filename): FileResponse
    {
        return new FileResponse(self::PDF, 'application/pdf', $filename);
    }
}
