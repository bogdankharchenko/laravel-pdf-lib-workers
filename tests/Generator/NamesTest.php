<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Tests\Generator;

use BogdanKharchenko\PdfLibWorkers\Generator\Names;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class NamesTest extends TestCase
{
    /**
     * @return iterable<array{string, string}>
     */
    public static function cases(): iterable
    {
        yield ['bottom-left', 'BottomLeft'];
        yield ['fillAndOutline', 'FillAndOutline'];
        yield ['Helvetica-BoldOblique', 'HelveticaBoldOblique'];
        yield ['image/png', 'ImagePng'];
        yield ['BASIC WL', 'BasicWl'];
        yield ['UseOutlines', 'UseOutlines'];
        yield ['L2R', 'L2R'];
        yield ['3B', '_3B'];
        yield ['4A0', '_4A0'];
        yield ['RC4-40', 'RC4_40'];
        yield ['1.7', '_1_7'];
    }

    #[DataProvider('cases')]
    public function test_names_an_enum_case_for_a_value(string $value, string $case): void
    {
        $this->assertSame($case, Names::enumCase($value));
    }

    public function test_refuses_a_value_with_nothing_to_name(): void
    {
        $this->expectExceptionObject(new RuntimeException('Cannot name an enum case for value "--"'));

        Names::enumCase('--');
    }

    public function test_refuses_values_that_would_share_a_case(): void
    {
        $this->assertSame(['top-left' => 'TopLeft', 'center' => 'Center'], Names::enumCases(['top-left', 'center'], 'Position'));

        $this->expectExceptionObject(new RuntimeException('Enum Position: values collide as case TopLeft'));

        Names::enumCases(['top-left', 'top_left'], 'Position');
    }
}
