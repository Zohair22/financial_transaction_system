<?php

namespace Modules\Plaid\Interfaces;

use Modules\Plaid\DTO\CreateLinkTokenDTO;
use Modules\Plaid\DTO\ExchangePublicTokenDTO;
use Modules\Plaid\DTO\FetchPlaidAccountsDTO;
use Modules\Plaid\DTO\FetchPlaidTransactionsDTO;
use Modules\Plaid\DTO\GetLinkTokenDTO;
use Modules\Plaid\DTO\PlaidWebhookPayloadDTO;
use Modules\Plaid\DTO\SyncPlaidTransactionsDTO;

/**
 * Application-facing Plaid orchestration: link token, item lifecycle, transactions, mapping, and webhooks.
 */
interface IPlaidServices
{
    /**
     * Create a Link token for Plaid Link (`/link/token/create`).
     *
     * @return array<string, mixed>
     */
    public function createLinkToken(CreateLinkTokenDTO $dto): array;

    /**
     * Retrieve an existing Link token (`/link/token/get`).
     *
     * @return array<string, mixed>
     */
    public function getLinkToken(GetLinkTokenDTO $dto): array;

    /**
     * Exchange a public token for an access token (`/item/public_token/exchange`).
     *
     * @return array<string, mixed>
     */
    public function exchangePublicToken(ExchangePublicTokenDTO $dto): array;

    /**
     * Fetch accounts for an Item (`/accounts/get`).
     *
     * @return array<string, mixed>
     */
    public function fetchAccounts(FetchPlaidAccountsDTO $dto): array;

    /**
     * Cursor-based transaction sync (`/transactions/sync`).
     *
     * @return array<string, mixed>
     */
    public function syncTransactions(SyncPlaidTransactionsDTO $dto): array;

    /**
     * Sync, map to internal shape, and dispatch.
     *
     * @return array{mapped: array<string, mixed>, plaid: array<string, mixed>}
     */
    public function syncTransactionsAndIntegrate(SyncPlaidTransactionsDTO $dto): array;

    /**
     * Legacy transaction fetch (`/transactions/get`) using an inclusive date range (YYYY-MM-DD).
     *
     * @return array<string, mixed>
     */
    public function fetchTransactions(FetchPlaidTransactionsDTO $dto): array;

    /**
     * Validates a decoded Plaid JSON body and maps known shapes (e.g. `/transactions/sync`) to stable internal arrays.
     *
     * @param  array<string, mixed>  $response
     * @return array<string, mixed>
     */
    public function validateAndMapPlaidResponse(array $response): array;

    /**
     * Interprets a Plaid webhook payload for transaction sync decisions.
     *
     * @return array{should_sync: bool, item_id: ?string, webhook_type: string, webhook_code: string, raw: array<string, mixed>}
     */
    public function handleWebhookTriggeredSync(PlaidWebhookPayloadDTO $dto): array;
}
