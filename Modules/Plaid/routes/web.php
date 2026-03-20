<?php

use Illuminate\Support\Facades\Route;
use Modules\Plaid\Http\Controllers\PlaidController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('plaids', PlaidController::class)->names('plaid');
});
