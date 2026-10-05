<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Tests;

use BogdanKharchenko\PdfMill\Client;
use BogdanKharchenko\PdfMill\Data\InfoResponse;
use BogdanKharchenko\PdfMill\Data\StoredPdf;
use BogdanKharchenko\PdfMill\Enums\AddFormFieldType;
use BogdanKharchenko\PdfMill\Enums\BuiltInFont;
use BogdanKharchenko\PdfMill\Exceptions\UnauthorizedException;
use BogdanKharchenko\PdfMill\Facades\PdfMill;
use BogdanKharchenko\PdfMill\MergeSource;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Group;

/**
 * Runs against a real deployment. Results go to the default outputs/ keys,
 * which the recommended R2 rule deletes after 7 days.
 *
 *   PDFMILL_LIVE_URL=https://… PDFMILL_LIVE_KEY=… vendor/bin/phpunit --group live
 */
#[Group('live')]
final class LiveTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (self::env('URL') === null || self::env('KEY') === null) {
            $this->markTestSkipped('Set PDFMILL_LIVE_URL and PDFMILL_LIVE_KEY to run against a deployment.');
        }

        Http::allowStrayRequests();
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('pdfmill.url', self::env('URL') ?? 'https://pdf.test');
        $app['config']->set('pdfmill.key', self::env('KEY') ?? 'test-key');
    }

    public function test_makes_fills_reads_and_merges_a_pdf(): void
    {
        $made = PdfMill::create()
            ->drawText('Hello from Laravel', 72, 760, size: 18, font: BuiltInFont::HelveticaBold)
            ->addFormField(AddFormFieldType::Text, 'name', page: 1, x: 72, y: 700, width: 200, height: 24)
            ->addFormField(AddFormFieldType::Text, '7', page: 1, x: 72, y: 660, width: 200, height: 24)
            ->setMetadata(title: 'Live test', copyright: '© 2026 Acme', custom: ['MadeFor' => 'Laravel'])
            ->store();
        $this->assertInstanceOf(StoredPdf::class, $made);

        // A field named "7" is a PHP int key; it must still be sent as a JSON object key.
        $filled = PdfMill::edit($made->key)->fillForm(fields: ['name' => 'Ada', '7' => 'seven'])->store();
        $this->assertInstanceOf(StoredPdf::class, $filled);

        $info = PdfMill::info($filled->key);
        $this->assertInstanceOf(InfoResponse::class, $info);
        $this->assertSame('© 2026 Acme', $info->metadata->copyright);
        $this->assertSame(['MadeFor' => 'Laravel'], $info->metadata->custom);
        $values = array_column(array_map(fn ($field): array => ['name' => $field->name, 'value' => $field->value], $info->form->fields), 'value', 'name');
        $this->assertSame(['name' => 'Ada', 7 => 'seven'], $values);

        $this->assertStringContainsString('Hello from Laravel', PdfMill::text($filled->key)->pages[0]->text);

        $downloaded = PdfMill::download($filled->key);
        $this->assertStringStartsWith('%PDF-', $downloaded->contents);

        $merged = PdfMill::merge([MergeSource::contents($downloaded->contents, 'filled.pdf'), $made->key])->store();
        $this->assertSame(2, $merged->pageCount);

        $this->assertGreaterThan(0, PdfMill::measureText('Hello', size: 12)->width);
    }

    public function test_returns_the_pdf_itself(): void
    {
        $spec = Http::get(self::env('URL').'/openapi.json');
        $this->assertInstanceOf(Response::class, $spec);
        $version = $spec->json('info.version');
        if (! is_string($version) || version_compare($version, '0.2.0', '<')) {
            $this->markTestSkipped("The deployment runs pdfmill {$version}; returning the PDF itself needs 0.2.0.");
        }

        $file = PdfMill::create(pageCount: 2)->filename('Live test.pdf')->file();

        $this->assertStringStartsWith('%PDF-', $file->contents);
        $this->assertSame('application/pdf', $file->contentType);
        $this->assertSame('Live test.pdf', $file->filename);
        $this->assertSame(2, $file->pageCount);
        $this->assertNull($file->key);

        $kept = PdfMill::create()->file(storeAs: 'outputs/live-test-'.Str::uuid().'.pdf');
        $this->assertNotNull($kept->key);
        $this->assertSame($kept->contents, PdfMill::download($kept->key)->contents);
    }

    public function test_rejects_a_wrong_key(): void
    {
        $this->expectException(UnauthorizedException::class);

        (new Client(app(Factory::class), (string) self::env('URL'), 'wrong-key'))->measureText('x');
    }

    private static function env(string $name): ?string
    {
        $value = getenv("PDFMILL_LIVE_{$name}");

        return is_string($value) && $value !== '' ? rtrim($value, '/') : null;
    }
}
