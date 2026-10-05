<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Tests;

use BogdanKharchenko\PdfLibWorkers\Data\InfoResponse;
use BogdanKharchenko\PdfLibWorkers\Facades\PdfLib;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

/**
 * The app's own laravel-data settings must not change the API's field names.
 */
final class AppConfigTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('data.name_mapping_strategy', ['input' => SnakeCaseMapper::class, 'output' => SnakeCaseMapper::class]);
    }

    public function test_a_global_name_mapping_does_not_rename_request_fields(): void
    {
        $this->respondWith('create-stored');

        PdfLib::create()->drawText('Hi', 1, 2, maxWidth: 100)->linkTtl(60)->store();

        $this->assertSentJson('{"operations": [{"op": "drawText", "text": "Hi", "x": 1, "y": 2, "maxWidth": 100}], "output": {"linkTtl": 60}}');
    }

    public function test_a_global_name_mapping_does_not_rename_response_fields(): void
    {
        $this->respondWith('info');

        $info = PdfLib::info('in.pdf');

        $this->assertInstanceOf(InfoResponse::class, $info);
        $this->assertSame(2, $info->pageCount);
        $this->assertTrue($info->hasJavaScript);
    }
}
