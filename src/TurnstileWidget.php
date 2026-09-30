<?php

declare(strict_types=1);

namespace MrPunyapal\Turnstile;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use InvalidArgumentException;

final class TurnstileWidget
{
    public function __construct(
        private readonly ConfigRepository $config,
    ) {
    }

    /**
     * Build a self-contained HTML document for a NativePHP WebView.
     *
     * The document stores the latest token in a DOM event so the host page can
     * forward it to its Laravel API endpoint.
     *
     * @param array<string, scalar|null> $options
     */
    public function html(array $options = []): string
    {
        $siteKey = (string) ($options['sitekey'] ?? $this->config->get('turnstile.site_key', ''));
        $action = $options['action'] ?? null;
        $theme = $options['theme'] ?? 'auto';

        if ($siteKey === '') {
            throw new InvalidArgumentException('The Turnstile site key is not configured.');
        }

        if (! in_array($theme, ['auto', 'light', 'dark'], true)) {
            throw new InvalidArgumentException('The Turnstile theme must be auto, light, or dark.');
        }

        $config = [
            'sitekey' => $siteKey,
            'action' => $action,
            'theme' => $theme,
        ];

        $json = json_encode($config, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);

        return <<<HTML
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        html, body { margin: 0; padding: 0; background: transparent; }
        body { min-height: 70px; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body>
    <div id="turnstile"></div>
    <script>
        (() => {
            const options = {$json};
            const emit = (event, detail = {}) => {
                window.dispatchEvent(new CustomEvent('turnstile:' + event, { detail }));
            };

            let widgetId = null;

            window.addEventListener('turnstile:reset', () => {
                if (widgetId !== null && window.turnstile) {
                    window.turnstile.reset(widgetId);
                }
            });

            window.turnstileReady = new Promise((resolve) => {
                window.turnstileReadyResolve = resolve;
            });

            window.turnstileCallback = (token) => {
                emit('success', { token });
            };

            window.turnstileExpired = () => {
                emit('expired');
            };

            window.turnstileError = (code) => {
                emit('error', { code });
            };

            const render = () => {
                widgetId = turnstile.render('#turnstile', {
                    sitekey: options.sitekey,
                    theme: options.theme,
                    ...(options.action ? { action: options.action } : {}),
                    callback: window.turnstileCallback,
                    'expired-callback': window.turnstileExpired,
                    'error-callback': window.turnstileError,
                });
                window.turnstileReadyResolve(widgetId);
                emit('ready', { widgetId });
            };

            window.addEventListener('load', () => {
                if (window.turnstile) {
                    render();
                }
            });
        })();
    </script>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit" async defer></script>
</body>
</html>
HTML;
    }
}
