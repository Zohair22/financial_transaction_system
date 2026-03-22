<?php

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;
use Modules\Plaid\Http\Controllers\API\ApiPlaidController;

/*
| Authenticated JSON API (default guard). Install `laravel/sanctum` and use `auth:sanctum` if you need Bearer tokens.
*/

Route::middleware([Authenticate::class])->prefix('v1')->group(function () {
    Route::prefix('plaid')->group(function () {
        Route::post('link-token', [ApiPlaidController::class, 'createLinkToken'])->name('plaid.link-token');
        Route::post('link-token/retrieve', [ApiPlaidController::class, 'getLinkToken'])->name('plaid.link-token.retrieve');
        Route::post('public-token/exchange', [ApiPlaidController::class, 'exchangePublicToken'])->name('plaid.public-token.exchange');
        Route::post('accounts', [ApiPlaidController::class, 'fetchAccounts'])->name('plaid.accounts');
        Route::post('transactions/sync', [ApiPlaidController::class, 'syncTransactions'])->name('plaid.transactions.sync');
        Route::post('transactions/fetch', [ApiPlaidController::class, 'fetchTransactions'])->name('plaid.transactions.fetch');
    });
});

Route::prefix('v1')->group(function () {
    Route::post('plaid/webhook', [ApiPlaidController::class, 'webhook'])->name('plaid.webhook');
});
