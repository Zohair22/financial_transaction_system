<?php

namespace Modules\Plaid\DTO;

/**
 * Decoded Plaid webhook JSON body.
 */
readonly class PlaidWebhookPayloadDTO
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public array $payload,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        return new self($payload);
    }
}
