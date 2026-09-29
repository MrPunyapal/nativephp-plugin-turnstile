<?php

declare(strict_types=1);

namespace MrPunyapal\Turnstile\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use MrPunyapal\Turnstile\TurnstileServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            TurnstileServiceProvider::class,
        ];
    }
}
