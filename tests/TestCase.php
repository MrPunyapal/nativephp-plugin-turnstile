<?php

declare(strict_types=1);

namespace MrPunyapal\Turnstile\Tests;

use MrPunyapal\Turnstile\TurnstileServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            TurnstileServiceProvider::class,
        ];
    }
}
