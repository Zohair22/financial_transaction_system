<?php

namespace Modules\Plaid\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Plaid\DTO\CreateLinkTokenDTO;
use Modules\Plaid\DTO\ExchangePublicTokenDTO;
use Modules\Plaid\DTO\FetchPlaidAccountsDTO;
use Modules\Plaid\DTO\FetchPlaidTransactionsDTO;
use Modules\Plaid\DTO\GetLinkTokenDTO;
use Modules\Plaid\DTO\PlaidWebhookPayloadDTO;
use Modules\Plaid\DTO\SyncPlaidTransactionsDTO;
use Modules\Plaid\Http\Requests\CreateLinkTokenRequest;
use Modules\Plaid\Http\Requests\ExchangePublicTokenRequest;
use Modules\Plaid\Http\Requests\FetchPlaidAccountsRequest;
use Modules\Plaid\Http\Requests\FetchPlaidTransactionsRequest;
use Modules\Plaid\Http\Requests\GetLinkTokenRequest;
use Modules\Plaid\Http\Requests\SyncPlaidTransactionsRequest;
use Modules\Plaid\Interfaces\IPlaidServices;

class ApiPlaidController extends Controller
{
    public function __construct(
        protected IPlaidServices $plaid,
    ) {}

    /**
     * Create a Plaid Link token (`/link/token/create`). Uses the authenticated user's id and name for Plaid `user`.
     */
    public function createLinkToken(CreateLinkTokenRequest $request): JsonResponse
    {
        $user = $request->user();
        $dto = new CreateLinkTokenDTO(
            (string) $user->getAuthIdentifier(),
            $user->name,
        );

        return response()->json(['data' => $this->plaid->createLinkToken($dto)]);
    }

    /**
     * Retrieve an existing Link token (`/link/token/get`).
     */
    public function getLinkToken(GetLinkTokenRequest $request): JsonResponse
    {
        $dto = GetLinkTokenDTO::fromValidated($request->validated());

        return response()->json(['data' => $this->plaid->getLinkToken($dto)]);
    }

    /**
     * Exchange a public token for an access token (`/item/public_token/exchange`).
     */
    public function exchangePublicToken(ExchangePublicTokenRequest $request): JsonResponse
    {
        $dto = ExchangePublicTokenDTO::fromValidated($request->validated());

        return response()->json(['data' => $this->plaid->exchangePublicToken($dto)]);
    }

    /**
     * Fetch accounts for an Item (`/accounts/get`).
     */
    public function fetchAccounts(FetchPlaidAccountsRequest $request): JsonResponse
    {
        $dto = FetchPlaidAccountsDTO::fromValidated($request->validated());

        return response()->json(['data' => $this->plaid->fetchAccounts($dto)]);
    }

    /**
     * Cursor-based transaction sync (`/transactions/sync`), then map and dispatch for persistence.
     */
    public function syncTransactions(SyncPlaidTransactionsRequest $request): JsonResponse
    {
        $dto = SyncPlaidTransactionsDTO::fromValidated($request->validated());

        return response()->json(['data' => $this->plaid->syncTransactionsAndIntegrate($dto)]);
    }

    /**
     * Legacy transaction fetch (`/transactions/get`).
     */
    public function fetchTransactions(FetchPlaidTransactionsRequest $request): JsonResponse
    {
        $dto = FetchPlaidTransactionsDTO::fromValidated($request->validated());

        return response()->json(['data' => $this->plaid->fetchTransactions($dto)]);
    }

    /**
     * Plaid webhook endpoint (no session auth; secure with Plaid JWT verification in production).
     */
    public function webhook(Request $request): JsonResponse
    {
        /** @var array<string, mixed> $payload */
        $payload = $request->all();
        $dto = PlaidWebhookPayloadDTO::fromArray($payload);

        return response()->json(['data' => $this->plaid->handleWebhookTriggeredSync($dto)]);
    }
}
