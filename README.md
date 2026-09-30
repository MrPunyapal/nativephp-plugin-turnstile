# NativePHP Turnstile

Cloudflare Turnstile integration for Laravel and NativePHP Mobile.

Turnstile runs in a browser environment. In NativePHP Mobile, use a WebView for the widget and send the resulting token to your Laravel backend. The backend must validate that token with Cloudflare's Siteverify API before allowing the protected action.

## Installation

```bash
composer require mrpunyapal/nativephp-plugin-turnstile
```

The package is auto-discovered by Laravel.

Publish the config when you need to override the defaults:

```bash
php artisan vendor:publish --tag=turnstile-config
```

Set your keys:

```dotenv
TURNSTILE_SITE_KEY=your-site-key
TURNSTILE_SECRET_KEY=your-secret-key
TURNSTILE_TIMEOUT=10
```

The site key is safe for the client. Never ship the secret key in the mobile application.

## Laravel API verification

A typical API endpoint receives the token from the NativePHP app and verifies it before doing the protected work:

```php
use Illuminate\Http\Request;
use MrPunyapal\Turnstile\Facades\Turnstile;

public function store(Request $request)
{
    $data = $request->validate([
        'turnstile_token' => ['required', 'string', 'max:2048'],
        // other request fields...
    ]);

    $result = Turnstile::verify(
        $data['turnstile_token'],
        $request->ip(),
    );

    abort_unless(
        $result->isValidFor(
            hostname: config('turnstile.hostname'),
            action: 'signup',
        ),
        422,
        'Turnstile verification failed.',
    );

    // Continue with the protected action...
}
```

The `hostname` check is optional for mobile flows where you intentionally do not use a hostname-specific policy. The `action` check is useful when the same widget/site key is used for several protected actions.

A failed Siteverify response is returned as `TurnstileResponse`; network errors, malformed Cloudflare responses, missing configuration, and invalid input throw `TurnstileException`.

## NativePHP Mobile

Cloudflare's mobile guidance requires a browser environment for Turnstile. Native applications should embed the widget in a WebView with JavaScript and DOM storage enabled and allow access to `challenges.cloudflare.com`.

### NativePHP Mobile v3

NativePHP v3 uses a web-view-first application model, so place Turnstile in the normal Blade/Livewire page rendered by the app.

### NativePHP Mobile v4

NativePHP v4 defaults to SuperNative, but the WebView component remains supported. Use a WebView for the Turnstile portion of your native screen or use the classic full-screen WebView architecture.

### Example widget

Explicit rendering is a good fit for mobile because you can request a fresh token immediately before the API call:

```html
<div id="turnstile"></div>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit"></script>
<script>
    let widgetId;

    turnstile.ready(() => {
        widgetId = turnstile.render('#turnstile', {
            sitekey: @js(config('turnstile.site_key')),
            action: 'signup',
            callback(token) {
                window.turnstileToken = token;
            },
            'expired-callback'() {
                window.turnstileToken = null;
            },
            'error-callback'() {
                window.turnstileToken = null;
            },
        });
    });
</script>
```

Send `window.turnstileToken` to your Laravel API over your normal authenticated request.

Do not trust the client-side callback as proof that the request is allowed. The backend must call Siteverify.

## Token lifecycle

Turnstile tokens are short-lived and single-use. Generate the token as close as possible to the protected API request and do not cache or persist tokens for later use. If Cloudflare returns `timeout-or-duplicate`, generate a fresh token and retry the protected action.

## Testing

Cloudflare provides testing sitekeys and secret keys so you can exercise success and failure cases without a real challenge.

Run the package test suite with:

```bash
composer test
```

Run the full checks with:

```bash
composer lint
```
