<?php

namespace Modules\Plaid\DTO;

/**
 * Input for Plaid `/accounts/get`.
 */
readonly class FetchPlaidAccountsDTO
{
    public function __construct(
        public string $accessToken,
        public ?int $userId = null,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated, ?int $userId = null): self
    {
        return new self(
            (string) $validated['access_token'],
            $userId,
        );
    }
}
