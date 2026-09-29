<?php

declare(strict_types=1);

return [
    'site_key' => env('TURNSTILE_SITE_KEY', ''),
    'secret_key' => env('TURNSTILE_SECRET_KEY', ''),
    'timeout' => (float) env('TURNSTILE_TIMEOUT', 10),
];
