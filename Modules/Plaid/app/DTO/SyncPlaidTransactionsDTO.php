<?php

namespace Modules\Plaid\DTO;

/**
 * Input for Plaid `/transactions/sync`.
 */
readonly class SyncPlaidTransactionsDTO
{
    public function __construct(
        public string $accessToken,
        public ?string $cursor = null,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated): self
    {
        $cursor = $validated['cursor'] ?? null;

        return new self(
            (string) $validated['access_token'],
            is_string($cursor) && $cursor !== '' ? $cursor : null,
        );
    }
}
