<?php

use Illuminate\Support\Facades\Route;
use Modules\BankTransferPayment\Http\Controllers\Admin\SettingsController;

/*
|--------------------------------------------------------------------------
| BankTransferPayment Admin Routes
|--------------------------------------------------------------------------
|
| Admin routes for bank transfer payment settings.
|
*/

Route::prefix('modules/payment/bank-transfer-payment')->name('admin.payment.bank-transfer.')->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});