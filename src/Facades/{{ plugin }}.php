<?php

declare(strict_types=1);

namespace {{ namespace }}\Facades;

use {{ namespace }}\Plugin;
use {{ namespace }}\Testing\BridgeFake;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array<string, mixed> example(array<string, mixed> $payload = [])
 * @method static array<string, mixed> manifest()
 * @method static bool isAvailable()
 *
 * @see Plugin
 */
final class {{ plugin }} extends Facade
{
    /**
     * @param array<string, array<string, mixed>|\Closure> $responses
     */
    public static function fake(array $responses = []): BridgeFake
    {
        $fake = new BridgeFake($responses);

        app()->instance('nativephp.mobile.bridge', $fake);

        return $fake;
    }

    protected static function getFacadeAccessor(): string
    {
        return '{{ package }}';
    }
}
