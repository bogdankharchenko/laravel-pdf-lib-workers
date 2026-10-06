<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Tests;

use BogdanKharchenko\PdfMill\Data\StoredPdf;
use BogdanKharchenko\PdfMill\Facades\PdfMill;
use BogdanKharchenko\PdfMill\Operations\FlattenForm;
use BogdanKharchenko\PdfMill\Operations\RotatePages;
use BogdanKharchenko\PdfMill\PendingPdf;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/**
 * create(), edit() and merge() return a PendingPdf: operations are chained, then sent.
 */
class PendingPdfTest extends TestCase
{
    private const PDF = "%PDF-1.7\n…";

    public function test_sends_nothing_until_asked_for_a_result(): void
    {
        Http::fake();

        $pending = PdfMill::edit('in.pdf')->rotatePages(90)->flattenForm();

        $this->assertInstanceOf(PendingPdf::class, $pending);
        Http::assertNothingSent();
    }

    public function test_stores_the_pdf_in_r2(): void
    {
        $this->respondWith('create-stored');

        $pdf = PdfMill::edit('in.pdf')->rotatePages(90)->flattenForm()->store();

        $this->assertInstanceOf(StoredPdf::class, $pdf);
        $this->assertSame('tests/fixtures/source.pdf', $pdf->key);
        $this->assertSame('https://pdf.test/pdf/edit', $this->sentRequest()->url());
        $this->assertSame(['application/json'], $this->sentRequest()->header('Accept'));
        $this->assertSentJson('{"source": "in.pdf", "operations": [{"op": "rotatePages", "degrees": 90}, {"op": "flattenForm"}]}');
    }

    public function test_stores_under_a_key_with_output_settings(): void
    {
        $this->respondWith('create-stored');

        PdfMill::create()->filename('Invoice 42.pdf')->linkTtl(86400)->withoutObjectStreams()->store('invoices/42.pdf');

        $this->assertSentJson(<<<'JSON'
            {"output": {"key": "invoices/42.pdf", "filename": "Invoice 42.pdf", "linkTtl": 86400, "useObjectStreams": false}}
            JSON);
    }

    public function test_keeps_the_fields_of_its_endpoint(): void
    {
        $this->respondWith('create-stored');

        PdfMill::edit('signed.pdf', incremental: true)->addJavaScript('init', 'app.alert(1);')->store();

        $this->assertSentJson(<<<'JSON'
            {"source": "signed.pdf", "incremental": true, "operations": [{"op": "addJavaScript", "name": "init", "script": "app.alert(1);"}]}
            JSON);
    }

    public function test_adds_operations_made_elsewhere(): void
    {
        $this->respondWith('create-stored');
        $steps = [new RotatePages(180), new FlattenForm];

        PdfMill::merge(['a.pdf', 'b.pdf'])->apply(...$steps)->pageNumbers()->store();

        $this->assertSentJson(<<<'JSON'
            {
                "sources": ["a.pdf", "b.pdf"],
                "operations": [{"op": "rotatePages", "degrees": 180}, {"op": "flattenForm"}, {"op": "pageNumbers"}]
            }
            JSON);
    }

    public function test_adds_operations_conditionally(): void
    {
        $this->respondWith('create-stored');

        PdfMill::edit('in.pdf')
            ->when(true, fn (PendingPdf $pdf) => $pdf->watermark(text: 'DRAFT'))
            ->unless(true, fn (PendingPdf $pdf) => $pdf->flattenForm())
            ->store();

        $this->assertSentJson('{"source": "in.pdf", "operations": [{"op": "watermark", "text": "DRAFT"}]}');
    }

    public function test_makes_the_browser_save_the_pdf(): void
    {
        Http::fake(['*' => Http::response(self::PDF, 200, ['Content-Type' => 'application/pdf'])]);

        $response = PdfMill::create()->download('blank.pdf');

        $this->assertSame(['application/pdf'], $this->sentRequest()->header('Accept'));
        $this->assertSentJson('{"output": {"store": false}}');
        $this->assertSame(self::PDF, $response->getContent());
        $this->assertSame('attachment; filename=blank.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_shows_the_pdf_when_returned_from_a_route(): void
    {
        Http::fake(['*' => Http::response(self::PDF, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="w9.pdf"',
        ])]);
        Route::get('/w9', fn () => PdfMill::edit('templates/w9.pdf')->fillForm(['name' => 'Ada'])->filename('w9.pdf'));

        $response = $this->get('/w9');

        $response->assertOk();
        $this->assertSame(self::PDF, $response->getContent());
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'inline; filename=w9.pdf');
        $this->assertSentJson(<<<'JSON'
            {
                "source": "templates/w9.pdf",
                "operations": [{"op": "fillForm", "fields": {"name": "Ada"}}],
                "output": {"filename": "w9.pdf", "store": false}
            }
            JSON);
    }
}
