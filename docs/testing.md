# Testing

The template uses Pest with Orchestra Testbench.

```bash
composer test
```

The included tests cover:

- Manifest JSON loading.
- Manifest bridge target shape.
- Placeholder documentation.
- Laravel service container registration.
- Facade dispatch into the NativePHP bridge binding.
- `BridgeFake` stubbing, call recording, and assertions.
- `isAvailable()` availability checks.

## Faking the bridge

No compiled mobile app is needed to test plugin behavior. `{{ plugin }}::fake()` binds a `BridgeFake` into the container in place of the real `nativephp.mobile.bridge`:

```php
use {{ namespace }}\Facades\{{ plugin }};

$fake = {{ plugin }}::fake([
    '{{ plugin }}.Example' => ['ok' => true],
]);

{{ plugin }}::example(['message' => 'hi']);

$fake->assertCalled('{{ plugin }}.Example', fn (array $payload): bool => $payload['message'] === 'hi');
```

Unstubbed calls return `[]` by default. Opt into strict mode with `preventStrayCalls()` to throw on any call that wasn't explicitly stubbed:

```php
{{ plugin }}::fake()->preventStrayCalls();
```

## Checking availability

Call `{{ plugin }}::isAvailable()` to check whether the NativePHP mobile bridge is bound before calling into it, so app code can no-op gracefully on web or desktop.

Static checks:

```bash
composer lint
composer analyse
composer rector:test
composer format
```
