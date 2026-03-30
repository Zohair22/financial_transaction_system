<?php

namespace Modules\Plaid\Interfaces;

use Modules\Plaid\DTO\CreateLinkTokenDTO;
use Modules\Plaid\DTO\ExchangePublicTokenDTO;
use Modules\Plaid\DTO\FetchPlaidAccountsDTO;
use Modules\Plaid\DTO\FetchPlaidTransactionsDTO;
use Modules\Plaid\DTO\SyncPlaidTransactionsDTO;
use Modules\Plaid\Models\PlaidItem;
use Modules\Plaid\Models\PlaidLinkToken;

interface IPlaidRepository
{
    public function storeLinkToken(CreateLinkTokenDTO $dto, array $response): ?PlaidLinkToken;

    public function storeExchangePublicToken(ExchangePublicTokenDTO $dto, array $response): ?PlaidItem;

    public function storeAccounts(FetchPlaidAccountsDTO $dto, array $response): void;

    public function storeLegacyTransactions(FetchPlaidTransactionsDTO $dto, array $response): void;

    public function storeTransactionsSync(SyncPlaidTransactionsDTO $dto, array $mapped, array $raw): void;
}
