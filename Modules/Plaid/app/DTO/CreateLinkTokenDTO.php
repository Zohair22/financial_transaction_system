<?php

namespace Modules\Plaid\DTO;

/**
 * Input for Plaid `/link/token/create`.
 */
readonly class CreateLinkTokenDTO
{
    public function __construct(
        public string $clientUserId,
        public ?string $clientName = null,
    ) {}

    /**
     * @param  array<string, mixed>  $validated  Validated request input (`client_user_id` optional, `client_name` optional).
     */
    public static function fromValidated(array $validated, string $defaultClientUserId): self
    {
        $clientUserId = isset($validated['client_user_id'])
            ? (string) $validated['client_user_id']
            : $defaultClientUserId;

        $clientName = array_key_exists('client_name', $validated)
            ? (is_string($validated['client_name']) ? $validated['client_name'] : null)
            : null;

        return new self($clientUserId, $clientName);
    }
}
