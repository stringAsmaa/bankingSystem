<?php

use App\Modules\Accounts\Controllers\AccountStateController;
use App\Modules\Accounts\Controllers\BankAccountController;
use App\Modules\Transactions\Controllers\DepositTransactionController;
use App\Modules\Transactions\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Transactions\Controllers\WithdrawalTransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ////////////////Account Module///////////////////
Route::prefix('accounts')->group(function () {
    // facade pattrens
    Route::post('/register', [BankAccountController::class, 'registerWithBankAccount']);

    Route::put('/{id}', [BankAccountController::class, 'update']);
    Route::patch('/{id}/close', [BankAccountController::class, 'close']);

    // State Design Pattrens & composite pattrens
    Route::patch('/{id}/deposit/{amount}', [AccountStateController::class, 'deposit']); // ايداع
    Route::patch('/{id}/withdraw/{amount}', [AccountStateController::class, 'withdraw']); // سحب
    Route::patch('/{id}/close', [AccountStateController::class, 'close']);
    Route::patch('/{id}/freeze', [AccountStateController::class, 'freeze']);
    Route::patch('/{id}/suspend', [AccountStateController::class, 'suspend']);
});

// ////////////////Transaction Module///////////////////

Route::prefix('transactions')->group(function () {
    Route::post('{id}/approve', [TransactionController::class, 'approveTransaction']);

});

Route::middleware('auth:api')->group(function () {
    Route::post('transactions/deposit', [DepositTransactionController::class, 'deposit']);
});

Route::get('/deposit/success', [DepositTransactionController::class, 'success'])->name('deposit.success');
Route::get('/deposit/cancel', [DepositTransactionController::class, 'cancel'])->name('deposit.cancel');

Route::prefix('transactions')->middleware('auth:api')->group(function () {
    Route::post('/withdraw', [WithdrawalTransactionController::class, 'withdraw']);
});

