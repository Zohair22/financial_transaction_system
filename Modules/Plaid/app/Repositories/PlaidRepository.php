<?php

namespace Modules\Plaid\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Plaid\DTO\CreateLinkTokenDTO;
use Modules\Plaid\DTO\ExchangePublicTokenDTO;
use Modules\Plaid\DTO\FetchPlaidAccountsDTO;
use Modules\Plaid\DTO\FetchPlaidTransactionsDTO;
use Modules\Plaid\DTO\SyncPlaidTransactionsDTO;
use Modules\Plaid\Interfaces\IPlaidRepository;
use Modules\Plaid\Models\PlaidAccount;
use Modules\Plaid\Models\PlaidItem;
use Modules\Plaid\Models\PlaidLinkToken;
use Modules\Plaid\Models\PlaidTransaction;
use Modules\Plaid\Models\PlaidTransactionSync;

class PlaidRepository implements IPlaidRepository
{
    public function storeLinkToken(CreateLinkTokenDTO $dto, array $response): ?PlaidLinkToken
    {
        if (! isset($response['link_token'])) {
            return null;
        }

        $expiresAt = isset($response['expiration'])
            ? Carbon::parse((string) $response['expiration'])
            : null;

        return PlaidLinkToken::create([
            'user_id' => (int) $dto->clientUserId,
            'link_token' => (string) $response['link_token'],
            'request_id' => $response['request_id'] ?? null,
            'expires_at' => $expiresAt,
            'meta' => $response,
        ]);
    }

    public function storeExchangePublicToken(ExchangePublicTokenDTO $dto, array $response): ?PlaidItem
    {
        if (! isset($response['access_token'], $response['item_id']) || $dto->userId === null) {
            return null;
        }

        return DB::transaction(function () use ($dto, $response) {
            $accessToken = (string) $response['access_token'];
            $hash = $this->hashAccessToken($accessToken);

            $linkToken = $this->markLatestLinkTokenAsUsed($dto->userId, $response['request_id'] ?? null);

            $item = PlaidItem::query()->updateOrCreate(
                ['item_id' => (string) $response['item_id']],
                [
                    'user_id' => $dto->userId,
                    'plaid_link_token_id' => $linkToken?->id,
                    'encrypted_access_token' => Crypt::encryptString($accessToken),
                    'access_token_hash' => $hash,
                    'request_id' => $response['request_id'] ?? null,
                    'status' => 'active',
                    'last_synced_at' => now(),
                ]
            );

            return $item;
        });
    }

    public function storeAccounts(FetchPlaidAccountsDTO $dto, array $response): void
    {
        $accounts = Arr::get($response, 'accounts');
        if (! is_array($accounts) || $accounts === []) {
            return;
        }

        DB::transaction(function () use ($dto, $response, $accounts) {
            $item = $this->resolveItemByAccessToken(
                $dto->accessToken,
                Arr::get($response, 'item'),
                $dto->userId
            );

            if (! $item) {
                return;
            }

            $this->refreshItemFromPayload($item, Arr::get($response, 'item'));

            foreach ($accounts as $accountPayload) {
                if (! is_array($accountPayload)) {
                    continue;
                }

                $this->upsertAccountFromPayload($item, $accountPayload);
            }
        });
    }

    public function storeLegacyTransactions(FetchPlaidTransactionsDTO $dto, array $response): void
    {
        $transactions = Arr::get($response, 'transactions');
        if (! is_array($transactions) || $transactions === []) {
            return;
        }

        DB::transaction(function () use ($dto, $response, $transactions) {
            $item = $this->resolveItemByAccessToken(
                $dto->accessToken,
                Arr::get($response, 'item'),
                $dto->userId
            );

            if (! $item) {
                return;
            }

            $this->refreshItemFromPayload($item, Arr::get($response, 'item'));

            $accountCache = $this->syncAccounts(
                $item,
                Arr::get($response, 'accounts', [])
            );

            $addedCount = $this->persistTransactions(
                $item,
                $transactions,
                $accountCache
            );

            $this->recordSyncSummary($item, [
                'cursor' => null,
                'has_more' => false,
                'request_id' => $response['request_id'] ?? null,
                'added_count' => $addedCount,
                'modified_count' => 0,
                'removed_count' => 0,
                'initiator' => 'transactions/get',
            ]);
        });
    }

    public function storeTransactionsSync(SyncPlaidTransactionsDTO $dto, array $mapped, array $raw): void
    {
        DB::transaction(function () use ($dto, $mapped, $raw) {
            $item = $this->resolveItemByAccessToken(
                $dto->accessToken,
                Arr::get($raw, 'item'),
                $dto->userId
            );

            if (! $item) {
                return;
            }

            $this->refreshItemFromPayload($item, Arr::get($raw, 'item'));

            $accountCache = $this->syncAccounts($item, Arr::get($mapped, 'accounts', []));

            $addedCount = $this->persistTransactions(
                $item,
                Arr::get($mapped, 'added', []),
                $accountCache
            );
            $modifiedCount = $this->persistTransactions(
                $item,
                Arr::get($mapped, 'modified', []),
                $accountCache
            );
            $removedCount = $this->markRemovedTransactions(
                Arr::get($mapped, 'removed', [])
            );

            $item->forceFill([
                'cursor' => $mapped['next_cursor'] ?? $item->cursor,
                'cursor_updated_at' => now(),
                'last_synced_at' => now(),
            ])->save();

            $this->recordSyncSummary($item, [
                'cursor' => $mapped['next_cursor'] ?? null,
                'has_more' => (bool) ($mapped['has_more'] ?? false),
                'request_id' => $mapped['request_id'] ?? null,
                'added_count' => $addedCount,
                'modified_count' => $modifiedCount,
                'removed_count' => $removedCount,
                'initiator' => 'transactions/sync',
            ]);
        });
    }

    private function syncAccounts(PlaidItem $item, array $accounts): array
    {
        $cache = [];
        foreach ($accounts as $accountPayload) {
            if (! is_array($accountPayload) || ! isset($accountPayload['account_id'])) {
                continue;
            }

            $account = $this->upsertAccountFromPayload($item, $accountPayload);
            $cache[$account->account_id] = $account;
        }

        return $cache;
    }

    private function upsertAccountFromPayload(PlaidItem $item, array $payload): PlaidAccount
    {
        $balances = Arr::get($payload, 'balances', []);

        return PlaidAccount::query()->updateOrCreate(
            ['account_id' => (string) $payload['account_id']],
            [
                'plaid_item_id' => $item->id,
                'name' => (string) Arr::get($payload, 'name', 'Unknown Account'),
                'official_name' => Arr::get($payload, 'official_name'),
                'mask' => Arr::get($payload, 'mask'),
                'type' => Arr::get($payload, 'type'),
                'subtype' => Arr::get($payload, 'subtype'),
                'holder_category' => Arr::get($payload, 'holder_category'),
                'balance_available' => Arr::get($balances, 'available'),
                'balance_current' => Arr::get($balances, 'current'),
                'balance_limit' => Arr::get($balances, 'limit'),
                'iso_currency_code' => Arr::get($balances, 'iso_currency_code'),
                'unofficial_currency_code' => Arr::get($balances, 'unofficial_currency_code'),
                'is_active' => true,
                'last_synced_at' => now(),
                'raw_balances' => $balances ?: null,
                'raw' => $payload,
            ]
        );
    }

    private function persistTransactions(
        PlaidItem $item,
        array $transactions,
        array &$accountCache
    ): int {
        $count = 0;

        foreach ($transactions as $transaction) {
            if (! is_array($transaction) || ! isset($transaction['transaction_id'], $transaction['account_id'])) {
                continue;
            }

            $account = $accountCache[$transaction['account_id']] ?? PlaidAccount::query()
                ->where('account_id', (string) $transaction['account_id'])
                ->first();

            if (! $account) {
                Log::warning('Plaid transaction skipped because account was not found.', [
                    'account_id' => $transaction['account_id'],
                    'transaction_id' => $transaction['transaction_id'],
                    'item_id' => $item->item_id,
                ]);

                continue;
            }

            $attributes = $this->mapTransactionAttributes($transaction, $account);

            PlaidTransaction::query()->updateOrCreate(
                ['transaction_id' => $attributes['transaction_id']],
                $attributes
            );
            $count++;
        }

        return $count;
    }

    private function markRemovedTransactions(array $removed): int
    {
        $count = 0;

        foreach ($removed as $transaction) {
            if (! is_array($transaction) || ! isset($transaction['transaction_id'])) {
                continue;
            }

            $model = PlaidTransaction::query()
                ->where('transaction_id', (string) $transaction['transaction_id'])
                ->first();

            if (! $model) {
                continue;
            }

            $model->update(['removed_at' => now()]);
            $count++;
        }

        return $count;
    }

    private function mapTransactionAttributes(array $transaction, PlaidAccount $account): array
    {
        $personalFinance = Arr::get($transaction, 'personal_finance_category', []);
        $category = Arr::get($transaction, 'category', []);

        return [
            'plaid_account_id' => $account->id,
            'transaction_id' => (string) $transaction['transaction_id'],
            'pending_transaction_id' => Arr::get($transaction, 'pending_transaction_id'),
            'amount' => $this->sanitizeAmount($transaction['amount'] ?? 0),
            'iso_currency_code' => Arr::get($transaction, 'iso_currency_code'),
            'unofficial_currency_code' => Arr::get($transaction, 'unofficial_currency_code'),
            'date' => Arr::get($transaction, 'date'),
            'authorized_date' => Arr::get($transaction, 'authorized_date'),
            'datetime' => Arr::get($transaction, 'datetime'),
            'authorized_datetime' => Arr::get($transaction, 'authorized_datetime'),
            'name' => (string) Arr::get($transaction, 'name', 'Unknown Transaction'),
            'merchant_name' => Arr::get($transaction, 'merchant_name'),
            'merchant_entity_id' => Arr::get($transaction, 'merchant_entity_id'),
            'payment_channel' => Arr::get($transaction, 'payment_channel'),
            'pending' => (bool) Arr::get($transaction, 'pending', false),
            'transaction_code' => Arr::get($transaction, 'transaction_code'),
            'transaction_type' => Arr::get($transaction, 'transaction_type'),
            'account_owner' => Arr::get($transaction, 'account_owner'),
            'category_primary' => Arr::get($personalFinance, 'primary') ?? Arr::get($category, 0),
            'category_detailed' => Arr::get($personalFinance, 'detailed') ?? Arr::get($category, 1),
            'personal_finance_confidence' => Arr::get($personalFinance, 'confidence_level'),
            'personal_finance_primary' => Arr::get($personalFinance, 'primary'),
            'personal_finance_detailed' => Arr::get($personalFinance, 'detailed'),
            'logo_url' => Arr::get($transaction, 'logo_url'),
            'website' => Arr::get($transaction, 'website'),
            'counterparties' => Arr::get($transaction, 'counterparties'),
            'location' => Arr::get($transaction, 'location'),
            'payment_meta' => Arr::get($transaction, 'payment_meta'),
            'raw' => $transaction,
        ];
    }

    private function refreshItemFromPayload(PlaidItem $item, ?array $payload): void
    {
        if (! is_array($payload)) {
            return;
        }

        $item->forceFill(array_filter([
            'institution_id' => Arr::get($payload, 'institution_id'),
            'institution_name' => Arr::get($payload, 'institution_name'),
            'webhook' => Arr::get($payload, 'webhook'),
            'available_products' => Arr::get($payload, 'available_products'),
            'billed_products' => Arr::get($payload, 'billed_products'),
            'consented_products' => Arr::get($payload, 'consented_products'),
            'update_type' => Arr::get($payload, 'update_type'),
            'error' => Arr::get($payload, 'error'),
        ], static fn ($value) => $value !== null))->save();
    }

    private function resolveItemByAccessToken(string $accessToken, ?array $itemPayload, ?int $userId): ?PlaidItem
    {
        $hash = $this->hashAccessToken($accessToken);
        $item = PlaidItem::query()->where('access_token_hash', $hash)->first();

        if ($item) {
            return $item;
        }

        if (isset($itemPayload['item_id'])) {
            $item = PlaidItem::query()->where('item_id', (string) $itemPayload['item_id'])->first();
        }

        if ($item) {
            return $item;
        }

        if ($userId !== null && isset($itemPayload['item_id'])) {
            return PlaidItem::query()->create([
                'user_id' => $userId,
                'item_id' => (string) $itemPayload['item_id'],
                'institution_id' => Arr::get($itemPayload, 'institution_id'),
                'institution_name' => Arr::get($itemPayload, 'institution_name'),
                'webhook' => Arr::get($itemPayload, 'webhook'),
                'available_products' => Arr::get($itemPayload, 'available_products'),
                'billed_products' => Arr::get($itemPayload, 'billed_products'),
                'consented_products' => Arr::get($itemPayload, 'consented_products'),
                'error' => Arr::get($itemPayload, 'error'),
                'update_type' => Arr::get($itemPayload, 'update_type'),
                'status' => 'active',
                'encrypted_access_token' => Crypt::encryptString($accessToken),
                'access_token_hash' => $hash,
            ]);
        }

        Log::warning('Unable to resolve Plaid Item for persistence.', [
            'user_id' => $userId,
            'item_id' => Arr::get($itemPayload, 'item_id'),
        ]);

        return null;
    }

    private function markLatestLinkTokenAsUsed(int $userId, ?string $requestId = null): ?PlaidLinkToken
    {
        $linkToken = PlaidLinkToken::query()
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->first();

        if (! $linkToken) {
            return null;
        }

        $linkToken->forceFill([
            'status' => 'used',
            'used_at' => now(),
            'request_id' => $requestId ?? $linkToken->request_id,
        ])->save();

        return $linkToken;
    }

    private function recordSyncSummary(PlaidItem $item, array $summary): void
    {
        PlaidTransactionSync::create([
            'plaid_item_id' => $item->id,
            'cursor' => $summary['cursor'],
            'has_more' => (bool) $summary['has_more'],
            'request_id' => $summary['request_id'],
            'added_count' => $summary['added_count'],
            'modified_count' => $summary['modified_count'],
            'removed_count' => $summary['removed_count'],
            'initiator' => $summary['initiator'],
            'summary' => $summary,
            'synced_at' => now(),
        ]);
    }

    private function sanitizeAmount(mixed $amount): string
    {
        return number_format((float) $amount, 4, '.', '');
    }

    private function hashAccessToken(string $token): string
    {
        return hash('sha256', $token);
    }
}
