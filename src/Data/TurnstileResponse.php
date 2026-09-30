<?php

declare(strict_types=1);

namespace MrPunyapal\Turnstile\Data;

final readonly class TurnstileResponse
{
    /** @param list<string> $errorCodes */
    public function __construct(
        public bool $success,
        public ?string $challengeTimestamp = null,
        public ?string $hostname = null,
        public ?string $action = null,
        public ?string $cdata = null,
        public array $errorCodes = [],
    ) {
    }

    /** @param array<string, mixed> $payload */
    public static function failure(array $errorCodes): self
    {
        return new self(false, errorCodes: $errorCodes);
    }

    public static function fromArray(array $payload): self
    {
        $errors = $payload['error-codes'] ?? [];

        return new self(
            success: ($payload['success'] ?? false) === true,
            challengeTimestamp: is_string($payload['challenge_ts'] ?? null) ? $payload['challenge_ts'] : null,
            hostname: is_string($payload['hostname'] ?? null) ? $payload['hostname'] : null,
            action: is_string($payload['action'] ?? null) ? $payload['action'] : null,
            cdata: is_string($payload['cdata'] ?? null) ? $payload['cdata'] : null,
            errorCodes: is_array($errors) ? array_values(array_filter($errors, 'is_string')) : [],
        );
    }

    public function failed(): bool
    {
        return ! $this->success;
    }

    public function hasError(string $code): bool
    {
        return in_array($code, $this->errorCodes, true);
    }

    public function hasHostname(string $hostname): bool
    {
        return $this->hostname === $hostname;
    }

    public function hasAction(string $action): bool
    {
        return $this->action === $action;
    }

    public function isValidFor(?string $hostname = null, ?string $action = null): bool
    {
        if (! $this->success) {
            return false;
        }

        if ($hostname !== null && ! $this->hasHostname($hostname)) {
            return false;
        }

        return $action === null || $this->hasAction($action);
    }
}
