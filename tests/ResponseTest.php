<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Tests;

use BogdanKharchenko\PdfLibWorkers\Data\AttachmentInfo;
use BogdanKharchenko\PdfLibWorkers\Data\Box;
use BogdanKharchenko\PdfLibWorkers\Data\DocumentMetadata;
use BogdanKharchenko\PdfLibWorkers\Data\DocumentScript;
use BogdanKharchenko\PdfLibWorkers\Data\ExtractedAttachment;
use BogdanKharchenko\PdfLibWorkers\Data\ExtractedGraphic;
use BogdanKharchenko\PdfLibWorkers\Data\ExtractedImage;
use BogdanKharchenko\PdfLibWorkers\Data\ExtractedPage;
use BogdanKharchenko\PdfLibWorkers\Data\FieldSettings;
use BogdanKharchenko\PdfLibWorkers\Data\FormField;
use BogdanKharchenko\PdfLibWorkers\Data\FormInfo;
use BogdanKharchenko\PdfLibWorkers\Data\InfoResponse;
use BogdanKharchenko\PdfLibWorkers\Data\LockedInfoResponse;
use BogdanKharchenko\PdfLibWorkers\Data\MeasuredLine;
use BogdanKharchenko\PdfLibWorkers\Data\PageBoxes;
use BogdanKharchenko\PdfLibWorkers\Data\PageInfo;
use BogdanKharchenko\PdfLibWorkers\Data\PageRange;
use BogdanKharchenko\PdfLibWorkers\Data\PageText;
use BogdanKharchenko\PdfLibWorkers\Data\SplitPart;
use BogdanKharchenko\PdfLibWorkers\Data\StoredPdf;
use BogdanKharchenko\PdfLibWorkers\Data\TextItem;
use BogdanKharchenko\PdfLibWorkers\Data\ViewerPreferences;
use BogdanKharchenko\PdfLibWorkers\Enums\Alignment;
use BogdanKharchenko\PdfLibWorkers\Enums\ExtractedImageMimeType;
use BogdanKharchenko\PdfLibWorkers\Enums\FormFieldType;
use BogdanKharchenko\PdfLibWorkers\Facades\PdfLib;
use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\LaravelData\Data;

/**
 * Responses captured from the real API, read into typed objects.
 */
final class ResponseTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string, Closure(): Data}>
     */
    public static function endpoints(): iterable
    {
        yield 'info' => ['info', '/pdf/info', fn () => PdfLib::info('in.pdf')];
        yield 'info, locked' => ['info-locked', '/pdf/info', fn () => PdfLib::info('locked.pdf')];
        yield 'text' => ['text-items', '/pdf/text', fn () => PdfLib::text('in.pdf', items: true)];
        yield 'extract, stored' => ['extract-stored', '/pdf/extract', fn () => PdfLib::extract('in.pdf')];
        yield 'extract, inline' => ['extract-inline', '/pdf/extract', fn () => PdfLib::extract('in.pdf', store: false)];
        yield 'scripts' => ['scripts', '/pdf/scripts', fn () => PdfLib::scripts('in.pdf')];
        yield 'measure' => ['measure', '/text/measure', fn () => PdfLib::measureText('Quarterly report for Acme Inc.', maxWidth: 140, fitHeight: 20)];
        yield 'split' => ['split', '/pdf/split', fn () => PdfLib::split('in.pdf')];
        yield 'create' => ['create-stored', '/pdf/create', fn () => PdfLib::create()->store()];
    }

    /**
     * @param  Closure(): Data  $call
     */
    #[DataProvider('endpoints')]
    public function test_reads_every_field_of_a_real_response(string $fixture, string $path, Closure $call): void
    {
        $this->respondWith($fixture);

        $result = $call();

        $this->assertSame("https://pdf.test{$path}", $this->sentRequest()->url());
        $this->assertContainsJson($this->fixture($fixture)['body'], $result->toArray());
    }

    public function test_info(): void
    {
        $this->respondWith('info');

        $info = PdfLib::info('in.pdf');

        $this->assertInstanceOf(InfoResponse::class, $info);
        $this->assertSame(2, $info->pageCount);
        $this->assertNull($info->pdfA);

        $page = $info->pages[0];
        $this->assertInstanceOf(PageInfo::class, $page);
        $this->assertInstanceOf(PageBoxes::class, $page->boxes);
        $this->assertInstanceOf(Box::class, $page->boxes->mediaBox);
        $this->assertSame(595.28, $page->boxes->mediaBox->width);

        $this->assertInstanceOf(DocumentMetadata::class, $info->metadata);
        $this->assertSame('© 2026 Acme', $info->metadata->copyright);
        $this->assertSame(['MadeFor' => 'Fixtures'], $info->metadata->custom);

        $this->assertInstanceOf(FormInfo::class, $info->form);
        [$text, $checkbox, $dropdown, $radio] = $info->form->fields;
        $this->assertInstanceOf(FormField::class, $text);
        $this->assertSame(FormFieldType::Text, $text->type);
        $this->assertSame('Ada', $text->value);
        $this->assertInstanceOf(FieldSettings::class, $text->settings);
        $this->assertSame(40, $text->settings->maxLength);
        $this->assertSame(Alignment::Left, $text->settings->alignment);
        $this->assertNull($text->settings->checked);
        $this->assertTrue($checkbox->value);
        $this->assertSame(['UK'], $dropdown->value);
        $this->assertSame(['basic', 'pro'], $radio->options);
        $this->assertSame([], $info->form->signatureFields);

        $this->assertInstanceOf(ViewerPreferences::class, $info->viewerPreferences);
        $this->assertInstanceOf(PageRange::class, $info->viewerPreferences->printPageRange[0] ?? null);
        $this->assertInstanceOf(AttachmentInfo::class, $info->attachments[0]);
        $this->assertSame('data.csv', $info->attachments[0]->name);
    }

    public function test_info_on_a_locked_pdf(): void
    {
        $this->respondWith('info-locked');

        $info = PdfLib::info('locked.pdf');

        $this->assertInstanceOf(LockedInfoResponse::class, $info);
        $this->assertTrue($info->needsPassword);
        $this->assertInstanceOf(PageInfo::class, $info->pages[0]);
    }

    public function test_text(): void
    {
        $this->respondWith('text-items');

        $page = PdfLib::text('in.pdf', items: true)->pages[0];

        $this->assertInstanceOf(PageText::class, $page);
        $this->assertSame('Invoice 42', $page->text);
        $this->assertInstanceOf(TextItem::class, $page->items[0] ?? null);
        $this->assertSame('Helvetica-Bold', $page->items[0]->fontFamily);
        $this->assertSame(72.0, $page->items[0]->x);
    }

    public function test_extract_with_stored_files(): void
    {
        $this->respondWith('extract-stored');

        $extracted = PdfLib::extract('in.pdf');

        $page = $extracted->pages[0];
        $this->assertInstanceOf(ExtractedPage::class, $page);
        $image = $page->images[0] ?? null;
        $this->assertInstanceOf(ExtractedImage::class, $image);
        $this->assertSame(ExtractedImageMimeType::ImagePng, $image->mimeType);
        $this->assertSame('tests/fixtures/extracted/page-1-image-1.png', $image->key);
        $this->assertNull($image->base64);
        $this->assertInstanceOf(ExtractedGraphic::class, $page->graphics[0] ?? null);
        $this->assertStringStartsWith('<svg', $page->graphics[0]->svg);

        $attachment = $extracted->attachments[0] ?? null;
        $this->assertInstanceOf(ExtractedAttachment::class, $attachment);
        $this->assertSame('tests/fixtures/extracted/attachments/data.csv', $attachment->key);
    }

    public function test_extract_with_inline_files(): void
    {
        $this->respondWith('extract-inline');

        $image = PdfLib::extract('in.pdf', store: false)->pages[0]->images[0] ?? null;

        $this->assertInstanceOf(ExtractedImage::class, $image);
        $this->assertNull($image->key);
        $this->assertNotNull($image->base64);
        $this->assertStringStartsWith("\x89PNG", (string) base64_decode($image->base64, true));
    }

    public function test_scripts(): void
    {
        $this->respondWith('scripts');

        $scripts = PdfLib::scripts('in.pdf');

        $this->assertInstanceOf(DocumentScript::class, $scripts->document[0]);
        $this->assertSame("app.alert('hi');", $scripts->document[0]->script);
        $this->assertSame([], $scripts->fields);
    }

    public function test_measure_text(): void
    {
        $this->respondWith('measure');

        $measured = PdfLib::measureText('Quarterly report for Acme Inc.', maxWidth: 140, fitHeight: 20);

        $this->assertInstanceOf(MeasuredLine::class, $measured->lines[0]);
        $this->assertSame('for Acme Inc.', $measured->lines[1]->text);
        $this->assertEqualsWithDelta(21.62, $measured->sizeForHeight, 0.01);
    }

    public function test_split(): void
    {
        $this->respondWith('split');

        $part = PdfLib::split('in.pdf')->parts[1];

        $this->assertInstanceOf(SplitPart::class, $part);
        $this->assertSame([2], $part->pages);
        $this->assertSame('tests/fixtures/split/part-002.pdf', $part->key);
    }

    public function test_a_stored_pdf(): void
    {
        $this->respondWith('create-stored');

        $pdf = PdfLib::create()->store();

        $this->assertInstanceOf(StoredPdf::class, $pdf);
        $this->assertSame(2, $pdf->pageCount);
        $this->assertStringStartsWith('https://pdf.test/files/tests/fixtures/source.pdf?', $pdf->url);
    }
}
