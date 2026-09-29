<?php

declare(strict_types=1);

namespace MrPunyapal\Turnstile\Contracts;

use MrPunyapal\Turnstile\Data\TurnstileResponse;

interface TurnstileContract
{
    public function siteKey(): string;

    public function verify(string $token, ?string $remoteIp = null, ?string $idempotencyKey = null): TurnstileResponse;
}
