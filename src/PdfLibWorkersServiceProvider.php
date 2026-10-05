<?php

declare(strict_types=1);

namespace BogdanKharchenko\PdfLibWorkers;

use BogdanKharchenko\PdfLibWorkers\Exceptions\MissingConfigurationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\ServiceProvider;

final class PdfLibWorkersServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pdf-lib-workers.php', 'pdf-lib-workers');

        $this->app->singleton(Client::class, function (Application $app): Client {
            /** @var array{url?: ?string, key?: ?string, timeout?: int, connect_timeout?: int} $config */
            $config = $app->make('config')->get('pdf-lib-workers', []);

            return new Client(
                $app->make(Factory::class),
                url: ($config['url'] ?? null) ?: throw MissingConfigurationException::for('url', 'PDF_LIB_WORKERS_URL'),
                key: ($config['key'] ?? null) ?: throw MissingConfigurationException::for('key', 'PDF_LIB_WORKERS_KEY'),
                timeout: $config['timeout'] ?? 120,
                connectTimeout: $config['connect_timeout'] ?? 10,
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/pdf-lib-workers.php' => config_path('pdf-lib-workers.php'),
        ], 'pdf-lib-workers-config');
    }
}
