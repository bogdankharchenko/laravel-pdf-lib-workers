<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Tests;

use BogdanKharchenko\PdfMill\Data\Layer;
use BogdanKharchenko\PdfMill\Data\Permissions;
use BogdanKharchenko\PdfMill\Enums\AddFormFieldType;
use BogdanKharchenko\PdfMill\Enums\BuiltInFont;
use BogdanKharchenko\PdfMill\Enums\ExtractInclude;
use BogdanKharchenko\PdfMill\Enums\Origin;
use BogdanKharchenko\PdfMill\Enums\PaperSize;
use BogdanKharchenko\PdfMill\Facades\PdfMill;
use BogdanKharchenko\PdfMill\FontSource;
use BogdanKharchenko\PdfMill\MergeSource;
use BogdanKharchenko\PdfMill\PdfSource;
use BogdanKharchenko\PdfMill\Source;

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
        PdfMill::edit('templates/in.pdf')->rotatePages(90)->store();

        $request = $this->sentRequest();
        $this->assertSame('POST', $request->method());
        $this->assertSame('https://pdf.test/pdf/edit', $request->url());
        $this->assertSame(['Bearer test-key'], $request->header('Authorization'));
        $this->assertSame(['application/json'], $request->header('Accept'));
        $this->assertSentJson('{"source": "templates/in.pdf", "operations": [{"op": "rotatePages", "degrees": 90}]}');
    }

    public function test_leaves_out_arguments_and_fields_that_were_not_given(): void
    {
        PdfMill::edit('in.pdf')->drawText('Hi', 10, 20)->store();

        $this->assertSentJson('{"source": "in.pdf", "operations": [{"op": "drawText", "text": "Hi", "x": 10, "y": 20}]}');
    }

    public function test_keeps_a_null_that_was_given(): void
    {
        PdfMill::edit('in.pdf')->addFormField(AddFormFieldType::Text, 'name', maxLength: null)->store();

        $this->assertSentJson('{"source": "in.pdf", "operations": [{"op": "addFormField", "type": "text", "name": "name", "maxLength": null}]}');
    }

    public function test_sends_enums_as_their_values(): void
    {
        PdfMill::create(PaperSize::Letter)
            ->drawText('Hi', 10, 20, origin: Origin::TopLeft, font: BuiltInFont::HelveticaBold)
            ->store();

        $this->assertSentJson(<<<'JSON'
            {
                "size": "Letter",
                "operations": [{"op": "drawText", "text": "Hi", "x": 10, "y": 20, "origin": "top-left", "font": "Helvetica-Bold"}]
            }
            JSON);
    }

    public function test_sends_a_custom_page_size_as_a_pair(): void
    {
        PdfMill::create([612, 792], pageCount: 2)->store();

        $this->assertSentJson('{"size": [612, 792], "pageCount": 2}');
    }

    public function test_sends_maps_as_objects_even_when_empty(): void
    {
        PdfMill::edit(PdfSource::url('https://files.test/in.pdf', headers: []))
            ->fillForm(fields: [])
            ->setMetadata(custom: [])
            ->store();

        $this->assertSentJson(<<<'JSON'
            {
                "source": {"url": "https://files.test/in.pdf", "headers": {}},
                "operations": [{"op": "fillForm", "fields": {}}, {"op": "setMetadata", "custom": {}}]
            }
            JSON);
    }

    public function test_sends_maps_with_numeric_keys_as_objects(): void
    {
        PdfMill::edit(PdfSource::url('https://files.test/in.pdf', headers: ['1' => 'x']))
            ->fillForm(fields: ['0' => 'first', '1' => true])
            ->setMetadata(custom: ['2026' => 'year'])
            ->store();

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
        PdfMill::edit('in.pdf')->encrypt('owner', permissions: new Permissions)->store();

        $this->assertSentJson('{"source": "in.pdf", "operations": [{"op": "encrypt", "ownerPassword": "owner", "permissions": {}}]}');
    }

    public function test_sends_a_list_of_objects(): void
    {
        PdfMill::edit('in.pdf')->setLayerVisibility([new Layer('Draft', false), new Layer('Final', true)])->store();

        $this->assertSentJson(<<<'JSON'
            {
                "source": "in.pdf",
                "operations": [{"op": "setLayerVisibility", "layers": [{"name": "Draft", "visible": false}, {"name": "Final", "visible": true}]}]
            }
            JSON);
    }

    public function test_sends_every_kind_of_source(): void
    {
        PdfMill::merge([
            'a.pdf',
            MergeSource::key('b.pdf', password: 'secret', pages: '1-2'),
            MergeSource::url('https://files.test/c.png', headers: ['authorization' => 'Bearer x'], size: 'image'),
            MergeSource::base64('ZA==', size: PaperSize::A5, margin: 18),
        ])
            ->watermark(text: 'DRAFT', font: FontSource::key('fonts/inter.ttf', postscriptName: 'Inter-Bold'))
            ->store();

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
        PdfMill::edit('in.pdf')->fillForm(images: ['signature' => Source::base64('iVBO'), 'logo' => 'logos/acme.png'])->store();

        $this->assertSentJson(<<<'JSON'
            {
                "source": "in.pdf",
                "operations": [{"op": "fillForm", "images": {"signature": {"base64": "iVBO"}, "logo": "logos/acme.png"}}]
            }
            JSON);
    }

    public function test_sends_lists_of_enums(): void
    {
        PdfMill::extract('in.pdf', pages: [1, -1], include: [ExtractInclude::Images, ExtractInclude::Text], store: false);

        $this->assertSentJson('{"source": "in.pdf", "pages": [1, -1], "include": ["images", "text"], "store": false}');
    }
}
