<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Tests;

use BogdanKharchenko\PdfMill\Enums\BuiltInFont;
use BogdanKharchenko\PdfMill\Operations\FillForm;
use BogdanKharchenko\PdfMill\Operations\RotatePages;
use BogdanKharchenko\PdfMill\Operations\Watermark;
use BogdanKharchenko\PdfMill\Source;
use BogdanKharchenko\PdfMill\Testing\SentRequest;

/**
 * hasOperation(): finding an operation by some of its fields.
 */
final class SentRequestTest extends TestCase
{
    private SentRequest $pdf;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pdf = new SentRequest('edit', ['source' => 'in.pdf', 'operations' => [
            ['op' => 'watermark', 'text' => 'DRAFT', 'opacity' => 0.5, 'font' => 'Helvetica-Bold'],
            ['op' => 'fillForm', 'fields' => ['name' => 'Ada', 'agree' => true]],
            ['op' => 'removePages', 'pages' => [2, 3]],
            ['op' => 'rotatePages', 'degrees' => 90],
        ]]);
    }

    public function test_matches_an_operation_by_name_and_some_of_its_fields(): void
    {
        $this->assertTrue($this->pdf->hasOperation('watermark'));
        $this->assertTrue($this->pdf->hasOperation('watermark', ['text' => 'DRAFT']));
        $this->assertFalse($this->pdf->hasOperation('watermark', ['text' => 'FINAL']));
        $this->assertFalse($this->pdf->hasOperation('watermark', ['scale' => 0.5]), 'A field it was not sent with');
        $this->assertFalse($this->pdf->hasOperation('pageNumbers'));
    }

    public function test_matches_nested_objects_by_their_fields_and_lists_in_full(): void
    {
        $this->assertTrue($this->pdf->hasOperation('fillForm', ['fields' => ['name' => 'Ada']]));
        $this->assertTrue($this->pdf->hasOperation('removePages', ['pages' => [2, 3]]));
        $this->assertFalse($this->pdf->hasOperation('removePages', ['pages' => [2]]));
        $this->assertFalse($this->pdf->hasOperation('removePages', ['pages' => [3, 2]]));
    }

    public function test_matches_numbers_and_enums_by_value(): void
    {
        $this->assertTrue($this->pdf->hasOperation('rotatePages', ['degrees' => 90.0]));
        $this->assertTrue($this->pdf->hasOperation('watermark', ['font' => BuiltInFont::HelveticaBold]));
        $this->assertFalse($this->pdf->hasOperation('rotatePages', ['degrees' => '90']));
    }

    public function test_matches_an_operation_built_as_your_code_builds_it(): void
    {
        $this->assertTrue($this->pdf->hasOperation(new Watermark(text: 'DRAFT', opacity: 0.5)));
        $this->assertTrue($this->pdf->hasOperation(new FillForm(['name' => 'Ada'])));
        $this->assertTrue($this->pdf->hasOperation(new RotatePages(90)));
        $this->assertFalse($this->pdf->hasOperation(new RotatePages(180)));
    }

    public function test_matches_an_upload_by_its_file_or_the_name_it_was_sent_under(): void
    {
        // The logo is file2 because a source was uploaded first: in the expected
        // operation alone it would be file1, so files are compared, not names.
        $pdf = new SentRequest('merge', ['sources' => [['upload' => 'file1']], 'operations' => [
            ['op' => 'watermark', 'image' => ['upload' => 'file2']],
        ]], [
            'file1' => ['filename' => 'id.pdf', 'contents' => '%PDF id'],
            'file2' => ['filename' => 'logo.png', 'contents' => 'PNG bytes'],
        ]);

        $this->assertTrue($pdf->hasOperation(new Watermark(image: Source::contents('PNG bytes', 'logo.png'))));
        $this->assertFalse($pdf->hasOperation(new Watermark(image: Source::contents('JPEG bytes', 'logo.png'))));
        $this->assertFalse($pdf->hasOperation(new Watermark(image: Source::contents('%PDF id', 'id.pdf'))));
        $this->assertTrue($pdf->hasOperation('watermark', ['image' => ['upload' => 'file2']]));
        $this->assertFalse($pdf->hasOperation('watermark', ['image' => ['upload' => 'file1']]));
    }

    public function test_other_endpoints_have_no_operations(): void
    {
        $this->assertSame([], (new SentRequest('info', ['source' => 'in.pdf']))->operations());
    }
}
