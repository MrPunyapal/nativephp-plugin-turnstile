# NativePHP Turnstile

Cloudflare Turnstile integration for Laravel backends used by NativePHP Mobile apps.

## Installation

```bash
composer require mrpunyapal/nativephp-plugin-turnstile
```

Set the Cloudflare keys in your Laravel backend:

```dotenv
TURNSTILE_SITE_KEY=your-site-key
TURNSTILE_SECRET_KEY=your-secret-key
TURNSTILE_TIMEOUT=10
```

Never put `TURNSTILE_SECRET_KEY` in the mobile app.

## NativePHP v3 and v4

Turnstile is a browser widget, not a native mobile SDK. Both NativePHP Mobile v3 and v4 can use it through a WebView.

For v3, render Turnstile in the normal Blade/Livewire page displayed by the app.

For v4, SuperNative is the default application architecture, but the WebView component is still available. Put the Turnstile widget in the WebView portion of the screen.

The widget helper generates a self-contained HTML document:

```php
use MrPunyapal\Turnstile\TurnstileWidget;

$html = app(TurnstileWidget::class)->html([
    'action' => 'signup',
    'size' => 'compact',
]);
```

Serve this HTML from an HTTPS hostname allowed by your Turnstile widget, then load that URL in a NativePHP WebView with JavaScript and DOM storage enabled. A hosted URL gives the challenge a real origin; do not rely on an opaque inline HTML origin. The document loads Cloudflare's Turnstile script from `challenges.cloudflare.com`.

On success the document emits a `turnstile:success` browser event containing the token. Expired, error, and timeout events are emitted as well. Generate the token immediately before the protected request.

For native URL-navigation callbacks, also pass a unique `cdata` state and a `return_path` pointing to a same-origin result endpoint. The widget navigates there with `state` and `status` in the query and the token only in the URL fragment. Validate the origin, result path, and state in the app. The API must still validate the token, expected hostname, action, and custom data with Siteverify. Reset the challenge after every submission attempt.

## Laravel API example

The mobile app should send the token to your Laravel API, not the Cloudflare secret:

```php
use Illuminate\Http\Request;
use MrPunyapal\Turnstile\Facades\Turnstile;

public function store(Request $request)
{
    $data = $request->validate([
        'turnstile_token' => ['required', 'string', 'max:2048'],
        'name' => ['required', 'string'],
    ]);

    $result = Turnstile::verify(
        $data['turnstile_token'],
        $request->ip(),
    );

    abort_unless(
        $result->isValidFor(
            action: 'signup',
        ),
        422,
        'Turnstile verification failed.',
    );

    // Protected operation...
}
```

Cloudflare's response includes the result, challenge timestamp, hostname, action, custom data, and error codes. Failed verification responses are returned normally so the API can decide how to respond. Network failures and invalid configuration/input throw `TurnstileException`.

## Security

A successful browser callback is not proof that the request is valid. The Laravel backend must call Cloudflare Siteverify.

Turnstile tokens are short-lived and single-use. Do not store them for later requests. A `timeout-or-duplicate` response means the client should obtain a fresh token.

For mobile authentication or account creation, keep your normal API authentication, authorization, rate limiting, and server-side validation in place. Turnstile is an additional bot check, not a replacement for those controls.

## Testing

Run:

```bash
composer test
composer lint
```

Cloudflare provides test keys for CI and local automated testing.
