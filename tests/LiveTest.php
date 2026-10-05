<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Tests;

use BogdanKharchenko\PdfLibWorkers\Client;
use BogdanKharchenko\PdfLibWorkers\Data\InfoResponse;
use BogdanKharchenko\PdfLibWorkers\Data\StoredPdf;
use BogdanKharchenko\PdfLibWorkers\Enums\AddFormFieldType;
use BogdanKharchenko\PdfLibWorkers\Enums\BuiltInFont;
use BogdanKharchenko\PdfLibWorkers\Exceptions\UnauthorizedException;
use BogdanKharchenko\PdfLibWorkers\Facades\PdfLib;
use BogdanKharchenko\PdfLibWorkers\MergeSource;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Group;

/**
 * Runs against a real deployment. Results go to the default outputs/ keys,
 * which the recommended R2 rule deletes after 7 days.
 *
 *   PDF_LIB_WORKERS_LIVE_URL=https://… PDF_LIB_WORKERS_LIVE_KEY=… vendor/bin/phpunit --group live
 */
#[Group('live')]
final class LiveTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (self::env('URL') === null || self::env('KEY') === null) {
            $this->markTestSkipped('Set PDF_LIB_WORKERS_LIVE_URL and PDF_LIB_WORKERS_LIVE_KEY to run against a deployment.');
        }

        Http::allowStrayRequests();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('pdf-lib-workers.url', self::env('URL') ?? 'https://pdf.test');
        $app['config']->set('pdf-lib-workers.key', self::env('KEY') ?? 'test-key');
    }

    public function test_makes_fills_reads_and_merges_a_pdf(): void
    {
        $made = PdfLib::create()
            ->drawText('Hello from Laravel', 72, 760, size: 18, font: BuiltInFont::HelveticaBold)
            ->addFormField(AddFormFieldType::Text, 'name', page: 1, x: 72, y: 700, width: 200, height: 24)
            ->addFormField(AddFormFieldType::Text, '7', page: 1, x: 72, y: 660, width: 200, height: 24)
            ->setMetadata(title: 'Live test', copyright: '© 2026 Acme', custom: ['MadeFor' => 'Laravel'])
            ->store();
        $this->assertInstanceOf(StoredPdf::class, $made);

        // A field named "7" is a PHP int key; it must still be sent as a JSON object key.
        $filled = PdfLib::edit($made->key)->fillForm(fields: ['name' => 'Ada', '7' => 'seven'])->store();
        $this->assertInstanceOf(StoredPdf::class, $filled);

        $info = PdfLib::info($filled->key);
        $this->assertInstanceOf(InfoResponse::class, $info);
        $this->assertSame('© 2026 Acme', $info->metadata->copyright);
        $this->assertSame(['MadeFor' => 'Laravel'], $info->metadata->custom);
        $values = array_column(array_map(fn ($field): array => ['name' => $field->name, 'value' => $field->value], $info->form->fields), 'value', 'name');
        $this->assertSame(['name' => 'Ada', 7 => 'seven'], $values);

        $this->assertStringContainsString('Hello from Laravel', PdfLib::text($filled->key)->pages[0]->text);

        $downloaded = PdfLib::download($filled->key);
        $this->assertStringStartsWith('%PDF-', $downloaded->contents);

        $merged = PdfLib::merge([MergeSource::contents($downloaded->contents, 'filled.pdf'), $made->key])->store();
        $this->assertSame(2, $merged->pageCount);

        $this->assertGreaterThan(0, PdfLib::measureText('Hello', size: 12)->width);
    }

    public function test_returns_the_pdf_itself(): void
    {
        $spec = Http::get(self::env('URL').'/openapi.json');
        $this->assertInstanceOf(Response::class, $spec);
        $version = $spec->json('info.version');
        if (! is_string($version) || version_compare($version, '0.2.0', '<')) {
            $this->markTestSkipped("The deployment runs pdf-lib-workers {$version}; returning the PDF itself needs 0.2.0.");
        }

        $file = PdfLib::create(pageCount: 2)->filename('Live test.pdf')->file();

        $this->assertStringStartsWith('%PDF-', $file->contents);
        $this->assertSame('application/pdf', $file->contentType);
        $this->assertSame('Live test.pdf', $file->filename);
        $this->assertSame(2, $file->pageCount);
        $this->assertNull($file->key);

        $kept = PdfLib::create()->file(storeAs: 'outputs/live-test-'.Str::uuid().'.pdf');
        $this->assertNotNull($kept->key);
        $this->assertSame($kept->contents, PdfLib::download($kept->key)->contents);
    }

    public function test_rejects_a_wrong_key(): void
    {
        $this->expectException(UnauthorizedException::class);

        (new Client(app(Factory::class), (string) self::env('URL'), 'wrong-key'))->measureText('x');
    }

    private static function env(string $name): ?string
    {
        $value = getenv("PDF_LIB_WORKERS_LIVE_{$name}");

        return is_string($value) && $value !== '' ? rtrim($value, '/') : null;
    }
}
