<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserRegistrationController;
use App\Modules\Accounts\Controllers\AuthController;
use App\Modules\Accounts\Controllers\BankAccountController;
use App\Modules\Accounts\Controllers\AccountStateController;
use App\Modules\administratives\Controllers\ReportController;
use App\Modules\Transactions\Controllers\TransactionController;
use App\Modules\administratives\Controllers\DashboardController;
use App\Modules\Support\Controllers\TicketController;
use App\Modules\Transactions\Controllers\AuditLogController;
use App\Modules\Transactions\Controllers\DepositTransactionController;
use App\Modules\Transactions\Controllers\RecommendationController;
use App\Modules\Transactions\Controllers\RecurringTransactionController;
use App\Modules\Transactions\Controllers\TransactionSettingController;
use App\Modules\Transactions\Controllers\TransferTransactionController;
use App\Modules\Transactions\Controllers\WithdrawalTransactionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

/**
 * تسجيل مستخدم جديد لأي رول: teller, manager, admin
 */

Route::post('/registerUser', [UserRegistrationController::class, 'register']);




// ////////////////Account Module///////////////////
Route::prefix('accounts')->group(function () {
    // facade pattrens
    Route::post('/register', [BankAccountController::class, 'registerWithBankAccount']);
    Route::post('login', [AuthController::class, 'login']);

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

Route::middleware('auth:api')->prefix('transactions')->group(function () {

    Route::post('/deposit', [DepositTransactionController::class, 'deposit']);
    Route::post('/withdraw', [WithdrawalTransactionController::class, 'withdraw']);
    Route::post('/transfer', [TransferTransactionController::class, 'transfer']);
    Route::post('{id}/approve', [TransactionController::class, 'approveTransaction']);
    Route::post('/recurring', [RecurringTransactionController::class, 'store']);
});


Route::get('/deposit/success', [DepositTransactionController::class, 'success'])->name('deposit.success');
Route::get('/deposit/cancel', [DepositTransactionController::class, 'cancel'])->name('deposit.cancel');




// ////////////////administratives Module///////////////////

Route::middleware(['auth:api', 'role:Admin|Manager'])
    ->get('/dashboard', [DashboardController::class, 'index']);



Route::prefix('reports')->middleware('auth:api')->group(function () {
    Route::get('daily-transactions', [ReportController::class, 'dailyTransactions'])->middleware(['role:Admin|Manager']);
    Route::get('account-summaries', [ReportController::class, 'accountSummaries'])->middleware(['role:Admin|Manager']);
    Route::get('audit-logs', [ReportController::class, 'auditLogs'])->middleware(['role:Admin|Manager']);
});

//////////////////////////////// Support Module //////////////////////////////////////////

Route::prefix('tickets')->middleware('auth:api')->group(function () {
    Route::get('/', [TicketController::class, 'index']);
    Route::post('/', [TicketController::class, 'store'])->middleware('role:Client');
    Route::get('{ticket}', [TicketController::class, 'show']);
    Route::post('/reply/{ticket}', [TicketController::class, 'reply']);
    Route::patch('/close/{ticket}', [TicketController::class, 'close']);
});


Route::middleware('auth:api')->group(function () {
    Route::get('/transactions/recommendations',[RecommendationController::class, 'index']);
    Route::put('/transaction-settings', [TransactionSettingController::class, 'update']);
    Route::get('/audit-logs', [AuditLogController::class, 'index']);//->middleware('role:Admin');
});

