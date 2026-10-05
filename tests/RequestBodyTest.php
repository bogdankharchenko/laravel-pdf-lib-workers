<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Tests;

use BogdanKharchenko\PdfLibWorkers\Data\Layer;
use BogdanKharchenko\PdfLibWorkers\Data\Output;
use BogdanKharchenko\PdfLibWorkers\Data\Permissions;
use BogdanKharchenko\PdfLibWorkers\Enums\AddFormFieldType;
use BogdanKharchenko\PdfLibWorkers\Enums\BuiltInFont;
use BogdanKharchenko\PdfLibWorkers\Enums\ExtractInclude;
use BogdanKharchenko\PdfLibWorkers\Enums\Origin;
use BogdanKharchenko\PdfLibWorkers\Enums\PaperSize;
use BogdanKharchenko\PdfLibWorkers\Facades\PdfLib;
use BogdanKharchenko\PdfLibWorkers\FontSource;
use BogdanKharchenko\PdfLibWorkers\MergeSource;
use BogdanKharchenko\PdfLibWorkers\Operations\AddFormField;
use BogdanKharchenko\PdfLibWorkers\Operations\DrawText;
use BogdanKharchenko\PdfLibWorkers\Operations\Encrypt;
use BogdanKharchenko\PdfLibWorkers\Operations\FillForm;
use BogdanKharchenko\PdfLibWorkers\Operations\RotatePages;
use BogdanKharchenko\PdfLibWorkers\Operations\SetLayerVisibility;
use BogdanKharchenko\PdfLibWorkers\Operations\SetMetadata;
use BogdanKharchenko\PdfLibWorkers\Operations\Watermark;
use BogdanKharchenko\PdfLibWorkers\PdfSource;
use BogdanKharchenko\PdfLibWorkers\Source;

/**
 * What goes over the wire for JSON requests.
 */
final class RequestBodyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->fakeEndpoints();
    }

    public function test_posts_json_with_the_api_key(): void
    {
        PdfLib::edit('templates/in.pdf', [new RotatePages(degrees: 90)]);

        $request = $this->sentRequest();
        $this->assertSame('POST', $request->method());
        $this->assertSame('https://pdf.test/pdf/edit', $request->url());
        $this->assertSame(['Bearer test-key'], $request->header('Authorization'));
        $this->assertSame(['application/json'], $request->header('Accept'));
        $this->assertSentJson('{"source": "templates/in.pdf", "operations": [{"op": "rotatePages", "degrees": 90}]}');
    }

    public function test_leaves_out_arguments_and_fields_that_were_not_given(): void
    {
        PdfLib::edit('in.pdf', [new DrawText('Hi', 10, 20)]);

        $this->assertSentJson('{"source": "in.pdf", "operations": [{"op": "drawText", "text": "Hi", "x": 10, "y": 20}]}');
    }

    public function test_keeps_a_null_that_was_given(): void
    {
        PdfLib::edit('in.pdf', [new AddFormField(AddFormFieldType::Text, 'name', maxLength: null)]);

        $this->assertSentJson('{"source": "in.pdf", "operations": [{"op": "addFormField", "type": "text", "name": "name", "maxLength": null}]}');
    }

    public function test_sends_enums_as_their_values(): void
    {
        PdfLib::create(PaperSize::Letter, operations: [
            new DrawText('Hi', 10, 20, origin: Origin::TopLeft, font: BuiltInFont::HelveticaBold),
        ]);

        $this->assertSentJson(<<<'JSON'
            {
                "size": "Letter",
                "operations": [{"op": "drawText", "text": "Hi", "x": 10, "y": 20, "origin": "top-left", "font": "Helvetica-Bold"}]
            }
            JSON);
    }

    public function test_sends_a_custom_page_size_as_a_pair(): void
    {
        PdfLib::create([612, 792], pageCount: 2);

        $this->assertSentJson('{"size": [612, 792], "pageCount": 2}');
    }

    public function test_sends_maps_as_objects_even_when_empty(): void
    {
        PdfLib::edit(PdfSource::url('https://files.test/in.pdf', headers: []), [
            new FillForm(fields: []),
            new SetMetadata(custom: []),
        ]);

        $this->assertSentJson(<<<'JSON'
            {
                "source": {"url": "https://files.test/in.pdf", "headers": {}},
                "operations": [{"op": "fillForm", "fields": {}}, {"op": "setMetadata", "custom": {}}]
            }
            JSON);
    }

    public function test_sends_maps_with_numeric_keys_as_objects(): void
    {
        PdfLib::edit(PdfSource::url('https://files.test/in.pdf', headers: ['1' => 'x']), [
            new FillForm(fields: ['0' => 'first', '1' => true]),
            new SetMetadata(custom: ['2026' => 'year']),
        ]);

        $this->assertSentJson(<<<'JSON'
            {
                "source": {"url": "https://files.test/in.pdf", "headers": {"1": "x"}},
                "operations": [
                    {"op": "fillForm", "fields": {"0": "first", "1": true}},
                    {"op": "setMetadata", "custom": {"2026": "year"}}
                ]
            }
            JSON);
    }

    public function test_sends_an_empty_nested_object_as_an_object(): void
    {
        PdfLib::edit('in.pdf', [new Encrypt('owner', permissions: new Permissions)]);

        $this->assertSentJson('{"source": "in.pdf", "operations": [{"op": "encrypt", "ownerPassword": "owner", "permissions": {}}]}');
    }

    public function test_sends_a_list_of_objects(): void
    {
        PdfLib::edit('in.pdf', [new SetLayerVisibility([new Layer('Draft', false), new Layer('Final', true)])]);

        $this->assertSentJson(<<<'JSON'
            {
                "source": "in.pdf",
                "operations": [{"op": "setLayerVisibility", "layers": [{"name": "Draft", "visible": false}, {"name": "Final", "visible": true}]}]
            }
            JSON);
    }

    public function test_sends_every_kind_of_source(): void
    {
        PdfLib::merge(
            [
                'a.pdf',
                MergeSource::key('b.pdf', password: 'secret', pages: '1-2'),
                MergeSource::url('https://files.test/c.png', headers: ['authorization' => 'Bearer x'], size: 'image'),
                MergeSource::base64('ZA==', size: PaperSize::A5, margin: 18),
            ],
            [new Watermark(text: 'DRAFT', font: FontSource::key('fonts/inter.ttf', postscriptName: 'Inter-Bold'))],
        );

        $this->assertSentJson(<<<'JSON'
            {
                "sources": [
                    "a.pdf",
                    {"key": "b.pdf", "password": "secret", "pages": "1-2"},
                    {"url": "https://files.test/c.png", "headers": {"authorization": "Bearer x"}, "size": "image"},
                    {"base64": "ZA==", "size": "A5", "margin": 18}
                ],
                "operations": [{"op": "watermark", "text": "DRAFT", "font": {"key": "fonts/inter.ttf", "postscriptName": "Inter-Bold"}}]
            }
            JSON);
    }

    public function test_sends_sources_inside_operations(): void
    {
        PdfLib::edit('in.pdf', [
            new FillForm(images: ['signature' => Source::base64('iVBO'), 'logo' => 'logos/acme.png']),
        ]);

        $this->assertSentJson(<<<'JSON'
            {
                "source": "in.pdf",
                "operations": [{"op": "fillForm", "images": {"signature": {"base64": "iVBO"}, "logo": "logos/acme.png"}}]
            }
            JSON);
    }

    public function test_sends_output_settings(): void
    {
        PdfLib::create(output: new Output(key: 'invoices/42.pdf', filename: 'Invoice 42.pdf', linkTtl: 600));

        $this->assertSentJson('{"output": {"key": "invoices/42.pdf", "filename": "Invoice 42.pdf", "linkTtl": 600}}');
    }

    public function test_sends_lists_of_enums(): void
    {
        PdfLib::extract('in.pdf', pages: [1, -1], include: [ExtractInclude::Images, ExtractInclude::Text], store: false);

        $this->assertSentJson('{"source": "in.pdf", "pages": [1, -1], "include": ["images", "text"], "store": false}');
    }
}
