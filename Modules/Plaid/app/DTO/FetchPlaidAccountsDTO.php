<?php

namespace Modules\Plaid\DTO;

/**
 * Input for Plaid `/accounts/get`.
 */
readonly class FetchPlaidAccountsDTO
{
    public function __construct(
        public string $accessToken,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated): self
    {
        return new self((string) $validated['access_token']);
    }
}
