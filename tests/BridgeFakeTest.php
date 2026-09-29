<?php

declare(strict_types=1);

use {{ namespace }}\Facades\{{ plugin }};
use {{ namespace }}\Testing\BridgeFake;
use PHPUnit\Framework\AssertionFailedError;

it('binds a fake bridge into the container and returns canned responses', function (): void {
    {{ plugin }}::fake([
        '{{ plugin }}.Example' => ['ok' => true],
    ]);

    expect({{ plugin }}::example(['message' => 'hi']))->toBe(['ok' => true]);
});

it('invokes closure stubs with the payload', function (): void {
    {{ plugin }}::fake([
        '{{ plugin }}.Example' => fn (array $payload): array => ['echo' => $payload],
    ]);

    expect({{ plugin }}::example(['message' => 'hi']))->toBe(['echo' => ['message' => 'hi']]);
});

it('returns an empty array for unstubbed calls by default', function (): void {
    $fake = {{ plugin }}::fake();

    expect($fake->call('{{ plugin }}.Missing', []))->toBe([]);
});

it('throws on unstubbed calls when stray calls are prevented', function (): void {
    $fake = (new BridgeFake)->preventStrayCalls();

    expect(fn (): array => $fake->call('{{ plugin }}.Missing', []))
        ->toThrow(RuntimeException::class, 'The {{ vendor }}/{{ package }} bridge received an unexpected call to [{{ plugin }}.Missing]. Stub it or allow stray calls.');
});

it('adds a stub after construction', function (): void {
    $fake = new BridgeFake;

    $fake->stub('{{ plugin }}.Example', ['stubbed' => true]);

    expect($fake->call('{{ plugin }}.Example', []))->toBe(['stubbed' => true]);
});

it('asserts a function was called, with and without a payload callback', function (): void {
    $fake = new BridgeFake;

    $fake->call('{{ plugin }}.Example', ['message' => 'hi']);

    $fake->assertCalled('{{ plugin }}.Example');
    $fake->assertCalled('{{ plugin }}.Example', fn (array $payload): bool => $payload['message'] === 'hi');
});

it('asserts a function was not called', function (): void {
    $fake = new BridgeFake;

    $fake->assertNotCalled('{{ plugin }}.Example');
});

it('asserts a function was called a given number of times', function (): void {
    $fake = new BridgeFake;

    $fake->call('{{ plugin }}.Example', []);
    $fake->call('{{ plugin }}.Example', []);

    $fake->assertCalledTimes('{{ plugin }}.Example', 2);
});

it('asserts nothing was called', function (): void {
    $fake = new BridgeFake;

    $fake->assertNothingCalled();
});

it('fails assertCalled when the function was never called', function (): void {
    $fake = new BridgeFake;

    expect(fn () => $fake->assertCalled('{{ plugin }}.Missing'))->toThrow(AssertionFailedError::class);
});

it('fails assertNotCalled when the function was called', function (): void {
    $fake = new BridgeFake;

    $fake->call('{{ plugin }}.Example', []);

    expect(fn () => $fake->assertNotCalled('{{ plugin }}.Example'))->toThrow(AssertionFailedError::class);
});

it('fails assertCalledTimes when the count does not match', function (): void {
    $fake = new BridgeFake;

    $fake->call('{{ plugin }}.Example', []);

    expect(fn () => $fake->assertCalledTimes('{{ plugin }}.Example', 2))->toThrow(AssertionFailedError::class);
});

it('fails assertNothingCalled when a call was recorded', function (): void {
    $fake = new BridgeFake;

    $fake->call('{{ plugin }}.Example', []);

    expect(fn () => $fake->assertNothingCalled())->toThrow(AssertionFailedError::class);
});

it('reports the bridge as unavailable with no binding and available after fake()', function (): void {
    expect({{ plugin }}::isAvailable())->toBeFalse();

    {{ plugin }}::fake();

    expect({{ plugin }}::isAvailable())->toBeTrue();
});
