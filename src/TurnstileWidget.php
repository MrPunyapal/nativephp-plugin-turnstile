<?php

declare(strict_types=1);

namespace MrPunyapal\Turnstile;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use InvalidArgumentException;
use JsonException;

final class TurnstileWidget
{
    public function __construct(
        private readonly ConfigRepository $config,
    ) {
    }

    /** @param array{sitekey?: string, action?: string|null, cdata?: string|null, theme?: string} $options */
    public function html(array $options = []): string
    {
        $siteKey = (string) ($options['sitekey'] ?? $this->config->get('turnstile.site_key', ''));
        $action = $options['action'] ?? null;
        $cdata = $options['cdata'] ?? null;
        $theme = $options['theme'] ?? 'auto';

        if ($siteKey === '') {
            throw new InvalidArgumentException('The Turnstile site key is not configured.');
        }

        if (! in_array($theme, ['auto', 'light', 'dark'], true)) {
            throw new InvalidArgumentException('The Turnstile theme must be auto, light, or dark.');
        }

        try {
            $json = json_encode(
                ['sitekey' => $siteKey, 'theme' => $theme, 'action' => $action, 'cdata' => $cdata],
                JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR,
            );
        } catch (JsonException $e) {
            throw new InvalidArgumentException('The Turnstile widget options could not be encoded.', previous: $e);
        }

        return <<<HTML
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
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
            let widgetId = null;

            window.turnstileWidget = {
                getResponse() {
                    return widgetId === null ? null : window.turnstile?.getResponse(widgetId) ?? null;
                },
                reset() {
                    if (widgetId !== null && window.turnstile) {
                        window.turnstile.reset(widgetId);
                    }
                }
            };

            const emit = (name, detail = {}) => {
                window.dispatchEvent(new CustomEvent('turnstile:' + name, { detail }));
            };

            const render = () => {
                widgetId = turnstile.render('#turnstile', {
                    sitekey: options.sitekey,
                    theme: options.theme,
                    ...(options.action ? { action: options.action } : {}),
                    ...(options.cdata ? { cData: options.cdata } : {}),
                    callback(token) {
                        emit('success', { token });
                    },
                    'expired-callback'() {
                        emit('expired');
                    },
                    'error-callback'(code) {
                        emit('error', { code });
                    },
                    'timeout-callback'() {
                        emit('timeout');
                    }
                });

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
