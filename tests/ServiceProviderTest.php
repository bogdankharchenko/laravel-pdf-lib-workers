<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill\Tests;

use BogdanKharchenko\PdfMill\Client;
use BogdanKharchenko\PdfMill\Exceptions\MissingConfigurationException;
use BogdanKharchenko\PdfMill\Facades\PdfMill;
use PHPUnit\Framework\Attributes\DataProvider;

final class ServiceProviderTest extends TestCase
{
    public function test_the_facade_and_container_share_one_client(): void
    {
        $this->assertInstanceOf(Client::class, PdfMill::getFacadeRoot());
        $this->assertSame(PdfMill::getFacadeRoot(), app(Client::class));
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
        config(['pdfmill.url' => $url]);
        $this->respondWith('info');

        PdfMill::info('in.pdf');

        $this->assertSame($sentTo, $this->sentRequest()->url());
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function settings(): iterable
    {
        yield ['url', 'pdfmill.url is not set. Add PDFMILL_URL to your .env file.'];
        yield ['key', 'pdfmill.key is not set. Add PDFMILL_KEY to your .env file.'];
    }

    #[DataProvider('settings')]
    public function test_says_which_setting_is_missing(string $setting, string $message): void
    {
        config(["pdfmill.{$setting}" => '']);

        $this->expectExceptionObject(new MissingConfigurationException($message));

        app(Client::class);
    }
}
