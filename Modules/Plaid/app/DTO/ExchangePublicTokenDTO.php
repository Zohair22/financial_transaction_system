<?php

namespace Modules\Plaid\DTO;

/**
 * Input for Plaid `/item/public_token/exchange`.
 */
readonly class ExchangePublicTokenDTO
{
    public function __construct(
        public string $publicToken,
        public ?int $userId = null,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated, ?int $userId = null): self
    {
        return new self(
            (string) $validated['public_token'],
            $userId,
        );
    }
}
