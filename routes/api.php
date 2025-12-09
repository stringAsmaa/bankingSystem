<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Accounts\Controllers\BankAccountController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('accounts')->group(function () {
    Route::post('/register', [BankAccountController::class, 'registerWithBankAccount']);
    Route::put('/{id}', [BankAccountController::class, 'update']);    // تعديل الحساب
    Route::patch('/{id}/close', [BankAccountController::class, 'close']); // إغلاق الحساب
});
