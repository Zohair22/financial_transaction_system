<?php

namespace Modules\Plaid\Services;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Modules\Plaid\DTO\CreateLinkTokenDTO;
use Modules\Plaid\DTO\ExchangePublicTokenDTO;
use Modules\Plaid\DTO\FetchPlaidAccountsDTO;
use Modules\Plaid\DTO\FetchPlaidTransactionsDTO;
use Modules\Plaid\DTO\GetLinkTokenDTO;
use Modules\Plaid\DTO\PlaidWebhookPayloadDTO;
use Modules\Plaid\DTO\SyncPlaidTransactionsDTO;
use Modules\Plaid\Events\PlaidTransactionsMapped;
use Modules\Plaid\Exceptions\PlaidApiException;
use Modules\Plaid\Interfaces\IClientPlaid;
use Modules\Plaid\Interfaces\IPlaidServices;
use Throwable;

/**
 * Coordinates Plaid HTTP calls, response validation/mapping, and domain events for downstream modules.
 */
class PlaidServices implements IPlaidServices
{
    public function __construct(
        protected IClientPlaid $plaid,
    ) {}

    // ================= PUBLIC API METHODS =================

    public function createLinkToken(CreateLinkTokenDTO $dto): array
    {
        $payload = [
            'user' => [
                'client_user_id' => $dto->clientUserId,
            ],
            'client_name' => $dto->clientName ?? (string) config('app.name', 'App'),
            'country_codes' => config('plaid.link.country_codes', ['US']),
            'language' => (string) config('plaid.link.language', 'en'),
            'products' => config('plaid.link.products', ['transactions', 'auth', 'identity', 'assets', 'income', 'accounts']),
        ];

        return $this->plaidPost('link/token/create', $payload);
    }

    public function getLinkToken(GetLinkTokenDTO $dto): array
    {
        return $this->plaidPost('link/token/get', [
            'link_token' => $dto->linkToken,
        ]);
    }

    public function exchangePublicToken(ExchangePublicTokenDTO $dto): array
    {
        return $this->plaidPost('item/public_token/exchange', [
            'public_token' => $dto->publicToken,
        ]);
    }

    public function fetchAccounts(FetchPlaidAccountsDTO $dto): array
    {
        return $this->plaidPost('accounts/get', [
            'access_token' => $dto->accessToken,
        ]);
    }

    public function fetchTransactions(FetchPlaidTransactionsDTO $dto): array
    {
        $startDate = $dto->startDate ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $dto->endDate ?? now()->format('Y-m-d');

        $payload = [
            'access_token' => $dto->accessToken,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        $response = $this->plaidPost('transactions/get', $payload);

        return $response;
    }

    public function syncTransactions(SyncPlaidTransactionsDTO $dto): array
    {
        $payload = [
            'access_token' => $dto->accessToken,
        ];

        if ($dto->cursor !== null && $dto->cursor !== '') {
            $payload['cursor'] = $dto->cursor;
        }

        return $this->plaidPost('transactions/sync', $payload);
    }

    /**
     * @return array{mapped: array<string, mixed>, plaid: array<string, mixed>}
     */
    public function syncTransactionsAndIntegrate(SyncPlaidTransactionsDTO $dto): array
    {
        $raw = $this->syncTransactions($dto);
        $mapped = $this->validateAndMapPlaidResponse($raw);
        $this->integrateWithTransactionModule($mapped);

        return [
            'mapped' => $mapped,
            'plaid' => $raw,
        ];
    }

    /**
     * Interprets a Plaid webhook payload and returns whether a transactions sync should run (caller resolves access_token from item_id).
     *
     * @return array{should_sync: bool, item_id: ?string, webhook_type: string, webhook_code: string, raw: array<string, mixed>}
     */
    public function handleWebhookTriggeredSync(PlaidWebhookPayloadDTO $dto): array
    {
        $response = $dto->payload;
        $webhookType = (string) ($response['webhook_type'] ?? '');
        $webhookCode = (string) ($response['webhook_code'] ?? '');
        $itemId = isset($response['item_id']) ? (string) $response['item_id'] : null;

        $shouldSync = $webhookType === 'TRANSACTIONS'
            && in_array($webhookCode, [
                'DEFAULT_UPDATE',
                'SYNC_UPDATES_AVAILABLE',
                'INITIAL_UPDATE',
                'HISTORICAL_UPDATE',
            ], true);

        return [
            'should_sync' => $shouldSync,
            'item_id' => $itemId,
            'webhook_type' => $webhookType,
            'webhook_code' => $webhookCode,
            'raw' => $response,
        ];
    }

    /**
     * Validates a Plaid JSON response (no API error), then maps known shapes to a stable internal array for downstream use.
     *
     * @param  array<string, mixed>  $response
     * @return array<string, mixed>
     */
    public function validateAndMapPlaidResponse(array $response): array
    {
        $this->assertPlaidResponseHasNoError($response);

        if ($this->looksLikeTransactionsSyncResponse($response)) {
            return $this->mapTransactionsSyncResponse($response);
        }

        return $response;
    }

    // ================= INTERNAL METHODS =================

    /**
     * Performs a Plaid POST, ensures the body is a decoded array, and surfaces Plaid error payloads as {@see PlaidApiException}.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    private function plaidPost(string $endpoint, array $payload): array
    {
        try {
            $response = $this->plaid->postRequest($endpoint, $payload);

            $this->logRequestsAndResponses([
                'endpoint' => $endpoint,
                'payload' => $payload,
            ], is_array($response) ? $response : ['raw' => $response]);

            if (! is_array($response)) {
                throw new PlaidApiException('Plaid returned an invalid or empty response body.');
            }

            $this->assertPlaidResponseHasNoError($response);

            return $response;
        } catch (Throwable $e) {
            $this->handlePlaidApiErrors($e);
        }
    }

    private function handlePlaidApiErrors(Throwable $e): never
    {
        throw PlaidApiException::fromThrowable($e);
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function assertPlaidResponseHasNoError(array $response): void
    {
        if (isset($response['error_type']) || isset($response['error_code'])) {
            throw PlaidApiException::fromPlaidErrorArray($response);
        }

        if (isset($response['error']) && is_array($response['error'])) {
            throw PlaidApiException::fromPlaidErrorArray($response['error']);
        }
    }

    /**
     * @param  array<string, mixed>  $response
     */
    private function looksLikeTransactionsSyncResponse(array $response): bool
    {
        return isset($response['transactions']) || array_key_exists('next_cursor', $response);
    }

    /**
     * Normalizes Plaid `/transactions/sync`-style payloads.
     *
     * @param  array<string, mixed>  $response
     * @return array<string, mixed>
     */
    private function mapTransactionsSyncResponse(array $response): array
    {
        $transactions = $response['transactions'] ?? [];
        if (! is_array($transactions)) {
            $transactions = [];
        }

        return [
            'accounts' => is_array($response['accounts'] ?? null) ? $response['accounts'] : [],
            'added' => is_array($transactions['added'] ?? null) ? $transactions['added'] : [],
            'modified' => is_array($transactions['modified'] ?? null) ? $transactions['modified'] : [],
            'removed' => is_array($transactions['removed'] ?? null) ? $transactions['removed'] : [],
            'next_cursor' => $response['next_cursor'] ?? null,
            'has_more' => (bool) ($response['has_more'] ?? false),
            'request_id' => $response['request_id'] ?? null,
        ];
    }

    /**
     * Dispatches mapped Plaid data so the Transaction module (or other listeners) can persist idempotently.
     *
     * @param  array<string, mixed>  $response  Typically output of {@see validateAndMapPlaidResponse()}
     * @return array<string, mixed>
     */
    private function integrateWithTransactionModule(array $response): array
    {
        Event::dispatch(new PlaidTransactionsMapped($response));

        return $response;
    }

    /**
     * @param  array<string, mixed>  $request
     * @param  array<string, mixed>  $response
     */
    private function logRequestsAndResponses(array $request, array $response): void
    {
        Log::info('================ PLAID REQUEST ================');
        Log::info('Plaid Request:', $request);
        Log::info('================ PLAID RESPONSE ===============');
        Log::info('Plaid Response:', $response);
        Log::info('================================================');
    }
}
