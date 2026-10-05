<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers\Tests;

use BogdanKharchenko\PdfLibWorkers\Client;
use BogdanKharchenko\PdfLibWorkers\Exceptions\MissingConfigurationException;
use BogdanKharchenko\PdfLibWorkers\Facades\PdfLib;
use PHPUnit\Framework\Attributes\DataProvider;

final class ServiceProviderTest extends TestCase
{
    public function test_the_facade_and_container_share_one_client(): void
    {
        $this->assertInstanceOf(Client::class, PdfLib::getFacadeRoot());
        $this->assertSame(PdfLib::getFacadeRoot(), app(Client::class));
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function baseUrls(): iterable
    {
        yield ['https://pdf.test/', 'https://pdf.test/pdf/info'];
        yield ['https://example.test/pdf-api', 'https://example.test/pdf-api/pdf/info'];
    }

    #[DataProvider('baseUrls')]
    public function test_sends_requests_under_the_configured_url(string $url, string $sentTo): void
    {
        config(['pdf-lib-workers.url' => $url]);
        $this->respondWith('info');

        PdfLib::info('in.pdf');

        $this->assertSame($sentTo, $this->sentRequest()->url());
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function settings(): iterable
    {
        yield ['url', 'pdf-lib-workers.url is not set. Add PDF_LIB_WORKERS_URL to your .env file.'];
        yield ['key', 'pdf-lib-workers.key is not set. Add PDF_LIB_WORKERS_KEY to your .env file.'];
    }

    #[DataProvider('settings')]
    public function test_says_which_setting_is_missing(string $setting, string $message): void
    {
        config(["pdf-lib-workers.{$setting}" => '']);

        $this->expectExceptionObject(new MissingConfigurationException($message));

        app(Client::class);
    }
}
