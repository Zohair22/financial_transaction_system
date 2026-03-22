<?php

namespace Modules\Plaid\DTO;

/**
 * Input for Plaid `/transactions/get` (legacy date range).
 */
readonly class FetchPlaidTransactionsDTO
{
    public function __construct(
        public string $accessToken,
        public ?string $startDate = null,
        public ?string $endDate = null,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public static function fromValidated(array $validated): self
    {
        return new self(
            (string) $validated['access_token'],
            isset($validated['start_date']) ? (string) $validated['start_date'] : null,
            isset($validated['end_date']) ? (string) $validated['end_date'] : null,
        );
    }
}
