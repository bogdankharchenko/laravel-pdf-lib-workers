<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Tests;

use BogdanKharchenko\PdfMill\Client;
use BogdanKharchenko\PdfMill\Data\ErrorResponse;
use BogdanKharchenko\PdfMill\Data\ExtractResponse;
use BogdanKharchenko\PdfMill\Data\InfoResponse;
use BogdanKharchenko\PdfMill\Data\LockedInfoResponse;
use BogdanKharchenko\PdfMill\Data\MeasureResponse;
use BogdanKharchenko\PdfMill\Data\ScriptsResponse;
use BogdanKharchenko\PdfMill\Data\SplitResponse;
use BogdanKharchenko\PdfMill\Data\StoredPdf;
use BogdanKharchenko\PdfMill\Data\TextResponse;
use BogdanKharchenko\PdfMill\Enums\ExtractInclude;
use BogdanKharchenko\PdfMill\Exceptions\InvalidRequestException;
use BogdanKharchenko\PdfMill\Exceptions\SourceUnavailableException;
use BogdanKharchenko\PdfMill\Exceptions\UnprocessablePdfException;
use BogdanKharchenko\PdfMill\Facades\PdfMill;
use BogdanKharchenko\PdfMill\FileResponse;
use BogdanKharchenko\PdfMill\MergeSource;
use BogdanKharchenko\PdfMill\Source;
use BogdanKharchenko\PdfMill\Testing\PdfMillFake;
use BogdanKharchenko\PdfMill\Testing\SentRequest;
use Closure;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\ExpectationFailedException;
use Ramsey\Uuid\UuidInterface;
use ReflectionClassConstant;
use Spatie\LaravelData\Data;
use Throwable;

/**
 * PdfMill::fake(): the API's replies without the network, and assertions on what was sent.
 */
final class FakeTest extends TestCase
{
    public function test_needs_no_configuration(): void
    {
        config(['pdfmill.url' => null, 'pdfmill.key' => null]);

        PdfMill::fake();

        $this->assertSame(1, PdfMill::create()->store()->pageCount);
    }

    public function test_fakes_a_client_injected_into_your_code(): void
    {
        $fake = PdfMill::fake();

        app(Client::class)->info('in.pdf');

        $this->assertSame($fake, app(Client::class));
        PdfMill::assertSent('info');
    }

    public function test_sends_nothing_over_the_network(): void
    {
        Http::fake();
        PdfMill::fake();

        PdfMill::merge(['a.pdf', 'b.pdf'])->store();

        Http::assertNothingSent();
        PdfMill::assertSent('merge');
    }

    /**
     * @return iterable<string, array{string, Closure(): object, class-string}>
     */
    public static function endpoints(): iterable
    {
        yield 'download' => ['download', fn () => PdfMill::download('outputs/a.pdf'), FileResponse::class];
        yield 'info' => ['info', fn () => PdfMill::info('in.pdf'), InfoResponse::class];
        yield 'text' => ['text', fn () => PdfMill::text('in.pdf', items: true), TextResponse::class];
        yield 'extract' => ['extract', fn () => PdfMill::extract('in.pdf'), ExtractResponse::class];
        yield 'scripts' => ['scripts', fn () => PdfMill::scripts('in.pdf'), ScriptsResponse::class];
        yield 'create' => ['create', fn () => PdfMill::create()->store(), StoredPdf::class];
        yield 'edit' => ['edit', fn () => PdfMill::edit('in.pdf')->file(), FileResponse::class];
        yield 'merge' => ['merge', fn () => PdfMill::merge(['a.pdf', 'b.pdf'])->store(), StoredPdf::class];
        yield 'split' => ['split', fn () => PdfMill::split('in.pdf'), SplitResponse::class];
        yield 'measureText' => ['measureText', fn () => PdfMill::measureText('Hello'), MeasureResponse::class];
    }

    /**
     * @param  Closure(): object  $call
     * @param  class-string  $class
     */
    #[DataProvider('endpoints')]
    public function test_every_endpoint_answers_by_default(string $endpoint, Closure $call, string $class): void
    {
        PdfMill::fake();

        $this->assertInstanceOf($class, $call());
        PdfMill::assertSent($endpoint, 1);
    }

    public function test_the_defaults_cover_every_endpoint(): void
    {
        $routes = (new ReflectionClassConstant(Client::class, 'ROUTES'))->getValue();
        $this->assertIsArray($routes);

        $this->assertEqualsCanonicalizing(array_keys($routes), array_keys(iterator_to_array(self::endpoints())));
    }

    public function test_stores_under_your_key_with_a_link_that_expires(): void
    {
        $this->freezeTime();
        PdfMill::fake();

        $pdf = PdfMill::create()->linkTtl(86400)->store('invoices/42 final.pdf');

        $expires = Date::now()->addDay()->utc();
        $this->assertSame('invoices/42 final.pdf', $pdf->key);
        $this->assertSame("https://pdfmill.test/files/invoices/42%20final.pdf?expires={$expires->getTimestamp()}&sig=fake", $pdf->url);
        $this->assertSame($expires->format('Y-m-d\TH:i:s.v\Z'), $pdf->expiresAt);
        $this->assertSame(1, $pdf->pageCount);
    }

    public function test_stores_under_a_new_key_by_default(): void
    {
        Str::freezeUuids(function (UuidInterface $uuid): void {
            PdfMill::fake();

            $this->assertSame("outputs/{$uuid}.pdf", PdfMill::create()->store()->key);
        });
    }

    public function test_gives_a_blank_page_as_the_file(): void
    {
        PdfMill::fake();

        $file = PdfMill::edit('in.pdf')->filename('Contract 42 – signed.pdf')->file();

        $this->assertStringStartsWith("%PDF-1.7\n", $file->contents);
        $this->assertSame(['application/pdf', 'Contract 42 – signed.pdf', 1], [$file->contentType, $file->filename, $file->pageCount]);
        $this->assertNull($file->key, 'A file not stored has no key');
        $this->assertNull($file->url);
    }

    public function test_the_blank_page_is_a_well_formed_pdf(): void
    {
        PdfMill::fake();

        $pdf = PdfMill::create()->file()->contents;

        $this->assertStringEndsWith("startxref\n".(strpos($pdf, "\nxref\n") + 1)."\n%%EOF\n", $pdf, 'startxref points at the cross-reference table');
        $this->assertSame(3, preg_match_all('/^(\d{10}) 00000 n $/m', $pdf, $offsets));
        foreach ($offsets[1] as $index => $offset) {
            $this->assertStringStartsWith(($index + 1).' 0 obj', substr($pdf, (int) $offset), "Object {$index} is where the table says");
        }
    }

    public function test_stores_the_file_too_when_asked(): void
    {
        PdfMill::fake();

        $file = PdfMill::create()->file(storeAs: 'contracts/42.pdf');

        $this->assertSame('contracts/42.pdf', $file->key);
        $this->assertStringStartsWith('https://pdfmill.test/files/contracts/42.pdf?expires=', (string) $file->url);
    }

    public function test_a_route_can_download_the_pdf(): void
    {
        PdfMill::fake();
        Route::get('/quote', fn () => PdfMill::create()->download('quote-7.pdf'));

        $this->get('/quote')->assertDownload('quote-7.pdf');
    }

    public function test_downloads_a_stored_file_by_its_key(): void
    {
        PdfMill::fake();

        $file = PdfMill::download('outputs/Q3 report.pdf');

        $this->assertSame('Q3 report.pdf', $file->filename);
        $this->assertStringStartsWith('%PDF', $file->contents);
        PdfMill::assertSent('download', fn (SentRequest $request) => $request->data === ['key' => 'outputs/Q3 report.pdf']);
    }

    public function test_describes_the_blank_page(): void
    {
        PdfMill::fake();

        $info = PdfMill::info('in.pdf');

        $this->assertInstanceOf(InfoResponse::class, $info);
        $this->assertSame([1, 595.28, 841.89], [$info->pageCount, $info->pages[0]->width, $info->pages[0]->height]);
        $this->assertSame([], $info->form->fields);
        $this->assertNull($info->metadata->title);
        $this->assertSame('', PdfMill::text('in.pdf')->pages[0]->text);
        $this->assertNull(PdfMill::text('in.pdf')->pages[0]->items, 'Items only when asked for');
        $this->assertSame([], PdfMill::text('in.pdf', items: true)->pages[0]->items);
    }

    public function test_extracts_what_was_asked_for(): void
    {
        PdfMill::fake();

        $default = PdfMill::extract('in.pdf');
        $text = PdfMill::extract('in.pdf', include: [ExtractInclude::Text]);

        $this->assertSame([[], null, []], [$default->pages[0]->images, $default->pages[0]->text, $default->attachments]);
        $this->assertSame(['', null, null], [$text->pages[0]->text, $text->pages[0]->images, $text->attachments]);
    }

    public function test_splits_into_a_part_per_range(): void
    {
        PdfMill::fake();

        $parts = PdfMill::split('in.pdf', ranges: ['1-3', '4-last'], prefix: 'contracts/42/')->parts;

        $this->assertSame(['contracts/42/part-001.pdf', 'contracts/42/part-002.pdf'], array_column($parts, 'key'));
        $this->assertSame([[1], [2]], array_column($parts, 'pages'));
    }

    public function test_measures_half_the_font_size_per_character(): void
    {
        PdfMill::fake();

        $measured = PdfMill::measureText("Total\nDue now", size: 10, lineHeight: 14, fitHeight: 20);

        $this->assertEquals([25, 35], array_column($measured->lines, 'width'));
        $this->assertEquals([35, 10, 24, 20], [$measured->width, $measured->height, $measured->blockHeight, $measured->sizeForHeight]);
    }

    public function test_changes_fields_of_the_default_reply(): void
    {
        PdfMill::fake([
            'merge' => ['pageCount' => 12],
            'info' => ['pageCount' => 3, 'metadata' => ['title' => 'Q3 report']],
            'text' => ['pages' => [['page' => 1, 'text' => 'Invoice 42'], ['page' => 2, 'text' => 'Total: 90']]],
        ]);

        $info = PdfMill::info('in.pdf');

        $this->assertSame(12, PdfMill::merge(['a.pdf'])->store()->pageCount);
        $this->assertSame(12, PdfMill::merge(['a.pdf'])->file()->pageCount);
        $this->assertInstanceOf(InfoResponse::class, $info);
        $this->assertSame([3, 'Q3 report', null], [$info->pageCount, $info->metadata->title, $info->metadata->author], 'Objects are merged');
        $this->assertSame(['Invoice 42', 'Total: 90'], array_column(PdfMill::text('in.pdf')->pages, 'text'), 'Lists are replaced');
    }

    /**
     * @return iterable<string, array{string, string, class-string<Data>, Closure(): object}>
     */
    public static function results(): iterable
    {
        yield 'info' => ['info', 'info', InfoResponse::class, fn () => PdfMill::info('in.pdf')];
        yield 'info, locked' => ['info', 'info-locked', LockedInfoResponse::class, fn () => PdfMill::info('in.pdf')];
        yield 'text' => ['text', 'text-items', TextResponse::class, fn () => PdfMill::text('in.pdf')];
        yield 'extract, stored' => ['extract', 'extract-stored', ExtractResponse::class, fn () => PdfMill::extract('in.pdf')];
        yield 'extract, inline' => ['extract', 'extract-inline', ExtractResponse::class, fn () => PdfMill::extract('in.pdf')];
        yield 'scripts' => ['scripts', 'scripts', ScriptsResponse::class, fn () => PdfMill::scripts('in.pdf')];
        yield 'measure' => ['measureText', 'measure', MeasureResponse::class, fn () => PdfMill::measureText('Hello')];
        yield 'split' => ['split', 'split', SplitResponse::class, fn () => PdfMill::split('in.pdf')];
        yield 'store' => ['create', 'create-stored', StoredPdf::class, fn () => PdfMill::create()->store()];
    }

    /**
     * @param  class-string<Data>  $class
     * @param  Closure(): object  $call
     */
    #[DataProvider('results')]
    public function test_replies_with_a_result_object(string $endpoint, string $fixture, string $class, Closure $call): void
    {
        $result = $class::from($this->fixture($fixture)['body']);
        PdfMill::fake([$endpoint => $result]);

        $this->assertSameData($result, $call());
    }

    public function test_replies_with_a_file(): void
    {
        $csv = new FileResponse("id,total\n42,90", 'text/csv', 'totals.csv');
        $signed = new FileResponse('%PDF-1.7 signed', 'application/pdf', 'signed.pdf', pageCount: 4);
        PdfMill::fake(['download' => $csv, 'edit' => $signed]);

        $stored = PdfMill::edit('in.pdf')->store('signed/42.pdf');

        $this->assertEquals($csv, PdfMill::download('extracted/totals.csv'));
        $this->assertEquals($signed, PdfMill::edit('in.pdf')->file());
        $this->assertSame(['signed/42.pdf', 15, 4], [$stored->key, $stored->size, $stored->pageCount]);
    }

    public function test_a_stored_pdf_reply_shapes_the_file_too(): void
    {
        $stored = new StoredPdf('contracts/42.pdf', 'https://cdn.test/42.pdf', '2026-01-01T00:00:00.000Z', 2048, 3);
        PdfMill::fake(['edit' => $stored]);

        $file = PdfMill::edit('in.pdf')->file(storeAs: 'contracts/42.pdf');

        $this->assertSameData($stored, PdfMill::edit('in.pdf')->store());
        $this->assertSame([3, 'contracts/42.pdf', 'https://cdn.test/42.pdf'], [$file->pageCount, $file->key, $file->url]);
    }

    public function test_replies_with_an_api_error(): void
    {
        $invalid = ErrorResponse::from($this->fixture('error-400-validation')['body']);
        PdfMill::fake([
            'edit' => new UnprocessablePdfException(422, new ErrorResponse('Not a PDF (starts with "hello")')),
            'merge' => new InvalidRequestException(400, $invalid),
        ]);

        $notPdf = $this->thrown(fn () => PdfMill::edit('in.pdf')->store());
        $badOptions = $this->thrown(fn () => PdfMill::merge(['a.pdf'])->store());

        $this->assertInstanceOf(UnprocessablePdfException::class, $notPdf);
        $this->assertSame([422, 'Not a PDF (starts with "hello")'], [$notPdf->status, $notPdf->getMessage()]);
        $this->assertInstanceOf(InvalidRequestException::class, $badOptions);
        $this->assertSameData($invalid, $badOptions->error);
    }

    public function test_replies_with_any_http_response(): void
    {
        PdfMill::fake(['info' => Http::response('<html>Bad gateway</html>', 502)]);

        $error = $this->thrown(fn () => PdfMill::info('in.pdf'));

        $this->assertInstanceOf(SourceUnavailableException::class, $error);
        $this->assertSame('HTTP 502: <html>Bad gateway</html>', $error->getMessage());
    }

    public function test_fails_to_send_with_any_other_exception(): void
    {
        $down = new ConnectionException('cURL error 28: Operation timed out');
        PdfMill::fake(['merge' => $down]);

        $this->assertSame($down, $this->thrown(fn () => PdfMill::merge(['a.pdf'])->store()));
        PdfMill::assertSent('merge');
    }

    public function test_replies_according_to_the_request(): void
    {
        PdfMill::fake([
            'info' => fn (SentRequest $request) => $request->data['source'] === 'locked.pdf'
                ? LockedInfoResponse::from($this->fixture('info-locked')['body'])
                : null,
        ]);

        $this->assertInstanceOf(LockedInfoResponse::class, PdfMill::info('locked.pdf'));
        $this->assertInstanceOf(InfoResponse::class, PdfMill::info('open.pdf'), 'null keeps the default');
    }

    public function test_rejects_a_reply_for_an_endpoint_that_does_not_exist(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException(
            'pdfmill has no endpoint [merg]; it has download, info, text, extract, scripts, create, edit, merge, split, measureText.',
        ));

        PdfMill::fake(['merg' => ['pageCount' => 2]]);
    }

    public function test_rejects_a_reply_the_endpoint_cannot_give(): void
    {
        $this->assertSame(
            'PdfMill::fake(): info replies with its result object or an array of fields, not '.FileResponse::class.'.',
            $this->thrown(fn () => PdfMill::fake(['info' => new FileResponse('id,total', 'text/csv', 'totals.csv')]))->getMessage(),
        );
        $this->assertSame(
            'PdfMill::fake(): download replies with a FileResponse, not array.',
            $this->thrown(fn () => PdfMill::fake(['download' => ['size' => 1]]))->getMessage(),
        );

        PdfMill::fake(['edit' => fn () => new TextResponse([])]);

        $this->assertSame(
            'PdfMill::fake(): edit replies with a StoredPdf, a FileResponse or an array of StoredPdf fields, not '.TextResponse::class.'.',
            $this->thrown(fn () => PdfMill::edit('in.pdf')->store())->getMessage(),
            'A closure\'s reply is checked when it is made',
        );
    }

    public function test_reads_and_records_uploads_as_production_sends_them(): void
    {
        Storage::fake();
        Storage::put('branding/logo.png', 'PNG bytes');
        PdfMill::fake();

        PdfMill::merge([MergeSource::file(UploadedFile::fake()->createWithContent('id.pdf', '%PDF id')), 'terms.pdf'])
            ->watermark(image: Source::disk('branding/logo.png'))
            ->store();

        PdfMill::assertSent('merge', fn (SentRequest $pdf) => $pdf->files === [
            'file1' => ['filename' => 'id.pdf', 'contents' => '%PDF id'],
            'file2' => ['filename' => 'logo.png', 'contents' => 'PNG bytes'],
        ]
            && $pdf->data['sources'] === [['upload' => 'file1'], 'terms.pdf']
            && $pdf->hasOperation('watermark', ['image' => ['upload' => 'file2']]));
    }

    public function test_asserts_what_was_sent(): void
    {
        PdfMill::fake();

        PdfMill::edit('in.pdf')->rotatePages(90)->store();
        PdfMill::edit('in.pdf')->flattenForm()->file();
        PdfMill::info('in.pdf');

        PdfMill::assertSent('edit');
        PdfMill::assertSent('edit', 2);
        PdfMill::assertSent('edit', fn (SentRequest $pdf) => $pdf->hasOperation('flattenForm'));
        PdfMill::assertSent(fn (SentRequest $request) => $request->endpoint === 'info');
        PdfMill::assertSent(fn (SentRequest $request) => $request->data['source'] === 'in.pdf', 3);
        PdfMill::assertNotSent('merge');
        PdfMill::assertNotSent('edit', fn (SentRequest $pdf) => $pdf->hasOperation('watermark'));
        PdfMill::assertSentCount(3);
        $this->assertSame(['edit', 'edit', 'info'], PdfMill::sent()->pluck('endpoint')->all());
        $this->assertSame(
            [[['op' => 'rotatePages', 'degrees' => 90]], [['op' => 'flattenForm']]],
            PdfMill::sent('edit')->map(fn (SentRequest $pdf) => $pdf->operations())->all(),
        );
    }

    public function test_a_failing_assertion_says_what_was_sent(): void
    {
        $fake = PdfMill::fake();

        PdfMill::assertNothingSent();
        $this->assertFailure('No merge request was sent. Nothing was sent.', fn () => $fake->assertSent('merge'));

        PdfMill::edit('in.pdf')->store();
        PdfMill::info('in.pdf');

        $this->assertFailure('No merge request was sent. Sent: edit, info.', fn () => $fake->assertSent('merge'));
        $this->assertFailure('Expected 2 edit requests, got 1. Sent: edit, info.', fn () => $fake->assertSent('edit', 2));
        $this->assertFailure('No edit request matching the callback was sent.', fn () => $fake->assertSent('edit', fn () => false));
        $this->assertFailure('No request matching the callback was sent.', fn () => $fake->assertSent(fn () => false));
        $this->assertFailure('Unexpected info request. Sent: edit, info.', fn () => $fake->assertNotSent('info'));
        $this->assertFailure('Unexpected request matching the callback.', fn () => $fake->assertNotSent(fn () => true));
        $this->assertFailure('Expected 1 request, got 2. Sent: edit, info.', fn () => $fake->assertSentCount(1));
        $this->assertFailure('Expected no requests. Sent: edit, info.', fn () => $fake->assertNothingSent());
    }

    public function test_an_assertion_rejects_an_endpoint_that_does_not_exist(): void
    {
        PdfMill::fake();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('pdfmill has no endpoint [merg]');

        PdfMill::assertNotSent('merg');
    }

    public function test_is_swapped_in_for_the_facade(): void
    {
        $this->assertFalse(PdfMill::isFake());

        $this->assertInstanceOf(PdfMillFake::class, PdfMill::fake());
        $this->assertInstanceOf(PdfMillFake::class, PdfMill::getFacadeRoot());
        $this->assertTrue(PdfMill::isFake());
    }

    /**
     * @param  non-empty-string  $message
     * @param  Closure(): void  $assertion
     */
    private function assertFailure(string $message, Closure $assertion): void
    {
        try {
            $assertion();
        } catch (ExpectationFailedException $failure) {
            $this->assertStringStartsWith($message, $failure->getMessage());

            return;
        }

        $this->fail("The assertion passed; expected it to fail with: {$message}");
    }

    /**
     * @param  Closure(): mixed  $call
     */
    private function thrown(Closure $call): Throwable
    {
        try {
            $call();
        } catch (Throwable $thrown) {
            return $thrown;
        }

        $this->fail('Nothing was thrown');
    }
}
