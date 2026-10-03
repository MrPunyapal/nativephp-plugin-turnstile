<?php

declare(strict_types=1);

namespace MrPunyapal\Turnstile;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use MrPunyapal\Turnstile\Contracts\TurnstileContract;

final class TurnstileServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/turnstile.php', 'turnstile');

        $this->app->singleton(TurnstileContract::class, fn (Application $app): Turnstile => new Turnstile(
            $app->make(ConfigRepository::class),
            $app->bound(ClientInterface::class) ? $app->make(ClientInterface::class) : new Client,
        ));

        $this->app->alias(TurnstileContract::class, Turnstile::class);
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/turnstile.php' => config_path('turnstile.php'),
        ], 'turnstile-config');
    }
}
