<?php

namespace Modules\Plaid\DTO;

/**
 * Input for Plaid `/link/token/get`.
 */
readonly class GetLinkTokenDTO
{
    public function __construct(
        public string $linkToken,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated): self
    {
        return new self((string) $validated['link_token']);
    }
}
