<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfMill;

use BogdanKharchenko\PdfMill\Exceptions\MissingConfigurationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\ServiceProvider;

class PdfMillServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pdfmill.php', 'pdfmill');

        $this->app->singleton(Client::class, function (Application $app): Client {
            /** @var array{url?: ?string, key?: ?string, timeout?: int, connect_timeout?: int} $config */
            $config = $app->make('config')->get('pdfmill', []);

            return new Client(
                $app->make(Factory::class),
                url: ($config['url'] ?? null) ?: throw MissingConfigurationException::for('url', 'PDFMILL_URL'),
                key: ($config['key'] ?? null) ?: throw MissingConfigurationException::for('key', 'PDFMILL_KEY'),
                timeout: $config['timeout'] ?? 120,
                connectTimeout: $config['connect_timeout'] ?? 10,
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/pdfmill.php' => config_path('pdfmill.php'),
        ], 'pdfmill-config');
    }
}
