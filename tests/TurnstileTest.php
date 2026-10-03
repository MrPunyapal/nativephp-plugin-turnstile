<?php

declare(strict_types=1);

namespace MrPunyapal\Turnstile\Tests;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Mockery;
use MrPunyapal\Turnstile\Contracts\TurnstileContract;
use MrPunyapal\Turnstile\Data\TurnstileResponse;
use MrPunyapal\Turnstile\Exceptions\TurnstileException;
use MrPunyapal\Turnstile\Facades\Turnstile;
use MrPunyapal\Turnstile\TurnstileWidget;

uses(TestCase::class);

it('supports a compact mobile challenge and rejects invalid widget sizes', function (): void {
    config(['turnstile.site_key' => 'test-site-key']);

    expect(app(TurnstileWidget::class)->html(['size' => 'compact']))->toContain('"size":"compact"');
    expect(fn () => app(TurnstileWidget::class)->html(['size' => 'invalid']))->toThrow(InvalidArgumentException::class);
});

it('returns the configured site key', function (): void {
    config()->set('turnstile.site_key', 'site-key');

    expect(Turnstile::siteKey())->toBe('site-key');
});

it('verifies a successful token with the siteverify API', function (): void {
    $client = Mockery::mock(ClientInterface::class);
    $client->expects('request')
        ->once()
        ->with('POST', 'https://challenges.cloudflare.com/turnstile/v0/siteverify', Mockery::on(
            fn (array $options): bool => $options['form_params'] === [
                'secret' => 'secret-key',
                'response' => 'token',
                'remoteip' => '127.0.0.1',
            ],
        ))
        ->andReturn(new Response(200, [], json_encode([
            'success' => true,
            'challenge_ts' => '2026-09-30T00:00:00.000Z',
            'hostname' => 'example.com',
            'action' => 'signup',
            'cdata' => 'abc',
            'error-codes' => [],
        ], JSON_THROW_ON_ERROR)));

    app()->instance(ClientInterface::class, $client);
    config()->set('turnstile.secret_key', 'secret-key');

    $result = app(TurnstileContract::class)->verify('token', '127.0.0.1');

    expect($result)
        ->toBeInstanceOf(TurnstileResponse::class)
        ->success->toBeTrue()
        ->isValidFor('example.com', 'signup')->toBeTrue();
});

it('returns a failed response without throwing for an invalid token', function (): void {
    $client = Mockery::mock(ClientInterface::class);
    $client->expects('request')->once()->andReturn(new Response(200, [], json_encode([
        'success' => false,
        'error-codes' => ['timeout-or-duplicate'],
    ], JSON_THROW_ON_ERROR)));

    app()->instance(ClientInterface::class, $client);
    config()->set('turnstile.secret_key', 'secret-key');

    $result = Turnstile::verify('token');

    expect($result->success)->toBeFalse()
        ->and($result->hasError('timeout-or-duplicate'))->toBeTrue()
        ->and($result->failed())->toBeTrue();
});

it('rejects an empty token', function (): void {
    config()->set('turnstile.secret_key', 'secret-key');

    expect(fn () => Turnstile::verify(''))->toThrow(TurnstileException::class);
});

it('rejects tokens longer than Cloudflare permits', function (): void {
    config()->set('turnstile.secret_key', 'secret-key');

    expect(fn () => Turnstile::verify(str_repeat('a', 2049)))->toThrow(TurnstileException::class);
});

it('rejects a missing secret key', function (): void {
    config()->set('turnstile.secret_key', '');

    expect(fn () => Turnstile::verify('token'))->toThrow(TurnstileException::class);
});

it('builds a WebView document with the configured site key and action', function (): void {
    config()->set('turnstile.site_key', 'site-key');

    $html = app(TurnstileWidget::class)->html([
        'action' => 'signup',
        'theme' => 'dark',
    ]);

    expect($html)
        ->toContain('site-key')
        ->toContain('signup')
        ->toContain('challenges.cloudflare.com/turnstile/v0/api.js?render=explicit');
});

it('returns native challenge results through a same-origin navigation with the token only in the fragment', function (): void {
    $html = app(TurnstileWidget::class)->html([
        'sitekey' => 'public-site-key',
        'action' => 'register',
        'cdata' => 'challenge-state',
        'return_path' => '/api/v1/captcha/result',
    ]);

    expect($html)->toContain('"returnPath":"\\/api\\/v1\\/captcha\\/result"')
        ->toContain("destination.searchParams.set('state', options.cdata ?? '')")
        ->toContain("destination.hash = name === 'success' ? encodeURIComponent(detail.token) : ''")
        ->not->toContain("searchParams.set('token'");
});

it('rejects unsafe return paths and unescaped widget values', function (string $path): void {
    expect(fn (): string => app(TurnstileWidget::class)->html(['sitekey' => 'key', 'return_path' => $path]))
        ->toThrow(InvalidArgumentException::class);
})->with(['https://evil.test', '//evil.test', '/result?token=x', '/result#token', '/result\\evil']);

it('escapes HTML and script delimiters in widget options', function (): void {
    $html = app(TurnstileWidget::class)->html(['sitekey' => '</script><script>alert(1)</script>']);
    expect($html)->not->toContain('</script><script>alert(1)</script>')
        ->toContain('\\u003C');
});
