<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Accounts\Controllers\BankAccountController;
use App\Modules\Accounts\Controllers\AccountStateController;
use App\Modules\Transactions\Controllers\DepositTransactionController;
use App\Modules\Transactions\Controllers\TransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('accounts')->group(function () {
    //facade pattrens
    Route::post('/register', [BankAccountController::class, 'registerWithBankAccount']);

    Route::put('/{id}', [BankAccountController::class, 'update']);
    Route::patch('/{id}/close', [BankAccountController::class, 'close']);

    //State Design Pattrens
    Route::patch('/{id}/deposit/{amount}', [AccountStateController::class, 'deposit']); //ايداع
    Route::patch('/{id}/withdraw/{amount}', [AccountStateController::class, 'withdraw']); //سحب
    Route::patch('/{id}/close', [AccountStateController::class, 'close']);
    Route::patch('/{id}/freeze', [AccountStateController::class, 'freeze']);
    Route::patch('/{id}/suspend', [AccountStateController::class, 'suspend']);
});

Route::prefix('transactions')->group(function () {
    Route::post('{id}/approve', [TransactionController::class, 'approveTransaction']);

});

Route::middleware('auth:api')->group(function () {
    Route::post('transactions/deposit', [DepositTransactionController::class, 'deposit']);
});

