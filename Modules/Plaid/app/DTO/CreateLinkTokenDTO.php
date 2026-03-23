<?php

namespace Modules\Plaid\DTO;

/**
 * Input for Plaid `/link/token/create`.
 *
 * `client_user_id` and `client_name` are always taken from the authenticated user (not request input).
 */
readonly class CreateLinkTokenDTO
{
    public function __construct(
        public string $clientUserId,
        public ?string $clientName = null,
    ) {}
}
