<?php

namespace App\Modules\Transactions\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Accounts\Models\BankAccount;
use App\Modules\accounts\Repositories\BankAccountRepositoryInterface;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Requests\DepositTransactionRequest;
use App\Modules\Transactions\Resources\TransactionResource;
use App\Modules\Transactions\Services\ApplyTransactionToAccountService;
use App\Modules\Transactions\Services\DepositTransactionService;
use App\Modules\Transactions\Services\TransactionRulesService;
use App\Modules\Transactions\Services\TransactionServiceHelper;
use Illuminate\Http\Request;

class DepositTransactionController extends Controller
{
    public function __construct(protected DepositTransactionService $service) {}

    public function deposit(DepositTransactionRequest $request)
    {
        $account = BankAccount::findOrFail($request->source_account_id);
        TransactionServiceHelper::ensureAccountOwnership($account, $request->user()->id);

        $transaction = $this->service->deposit(
            $request->validated() + ['user_id' => $request->user()->id]
        );
        return ApiResponse::sendResponse(200, "Deposit completed successfully.", new TransactionResource($transaction));
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        $transaction = Transaction::all()->firstWhere('metadata.checkout_session_id', $sessionId);

        if ($transaction->transaction_status === TransactionStatus::COMPLETED) {
            $applyService = new ApplyTransactionToAccountService(
                app(BankAccountRepositoryInterface::class)
            );
            $applyService->apply($transaction);
        }
        return response()->view('deposit.success', [
            'session_id' => $sessionId
        ]);
    }

    public function cancel()
    {
        return response()->view('deposit.cancel');
    }
}
