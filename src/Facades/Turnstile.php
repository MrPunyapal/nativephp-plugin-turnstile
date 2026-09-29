<?php

declare(strict_types=1);

namespace MrPunyapal\Turnstile\Facades;

use Illuminate\Support\Facades\Facade;
use MrPunyapal\Turnstile\Data\TurnstileResponse;
use MrPunyapal\Turnstile\Turnstile as TurnstileManager;

/**
 * @method static string siteKey()
 * @method static TurnstileResponse verify(string $token, ?string $remoteIp = null)
 *
 * @see TurnstileManager
 */
final class Turnstile extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return TurnstileManager::class;
    }
}
