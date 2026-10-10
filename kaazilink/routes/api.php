<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Payments\PayHeroCallbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Payment webhooks and external callbacks.
|
*/

Route::prefix('payments')->name('payments.')->group(function () {
    Route::post('/callback/payhero', PayHeroCallbackController::class)->name('callback.payhero');
});
