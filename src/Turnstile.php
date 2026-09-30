<?php

declare(strict_types=1);

namespace MrPunyapal\Turnstile;

use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use JsonException;
use MrPunyapal\Turnstile\Contracts\TurnstileContract;
use MrPunyapal\Turnstile\Data\TurnstileResponse;
use MrPunyapal\Turnstile\Exceptions\TurnstileException;

final class Turnstile implements TurnstileContract
{
    private const ENDPOINT = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public function __construct(
        private readonly ConfigRepository $config,
        private readonly ClientInterface $client,
    ) {
    }

    public function siteKey(): string
    {
        return (string) $this->config->get('turnstile.site_key', '');
    }

    public function endpoint(): string
    {
        return self::ENDPOINT;
    }

    public function verify(string $token, ?string $remoteIp = null): TurnstileResponse
    {
        if ($token === '') {
            throw new TurnstileException('A Turnstile token is required.');
        }

        $secret = (string) $this->config->get('turnstile.secret_key', '');

        if ($secret === '') {
            throw new TurnstileException('The Turnstile secret key is not configured.');
        }

        if (strlen($token) > 2048) {
            throw new TurnstileException('The Turnstile token exceeds the 2048 character limit.');
        }

        $data = [
            'secret' => $secret,
            'response' => $token,
        ];

        if ($remoteIp !== null && $remoteIp !== '') {
            $data['remoteip'] = $remoteIp;
        }

        try {
            $response = $this->client->request('POST', self::ENDPOINT, [
                'form_params' => $data,
                'timeout' => (float) $this->config->get('turnstile.timeout', 10),
            ]);
        } catch (\Throwable $e) {
            throw new TurnstileException('Turnstile verification request failed.', previous: $e);
        }

        try {
            /** @var array<string, mixed> $payload */
            $payload = json_decode(
                (string) $response->getBody(),
                true,
                flags: JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $e) {
            throw new TurnstileException('Turnstile returned an invalid response.', previous: $e);
        }

        return TurnstileResponse::fromArray($payload);
    }
}
