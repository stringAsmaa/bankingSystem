<?php

namespace App\Modules\Transactions\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Transactions\Requests\TransactionRequest;
use App\Modules\Transactions\Requests\WithdrawalTransactionRequest;
use App\Modules\Transactions\Services\WithdrawalTransactionService;
use App\Modules\Transactions\Resources\TransactionResource;
use App\Modules\Transactions\Services\TransactionServiceHelper;

class WithdrawalTransactionController extends Controller
{
    public function __construct(protected WithdrawalTransactionService $service) {}

    public function withdraw(WithdrawalTransactionRequest $request)
    {
        $account = BankAccount::findOrFail($request->source_account_id);
        TransactionServiceHelper::ensureAccountOwnership($account, $request->user()->id);

        $transaction = $this->service->withdraw(
            $request->validated() + ['user_id' => $request->user()->id]
        );
        return ApiResponse::sendResponse(200, 'Withdrawal completed successfully.', new TransactionResource($transaction));
    }
}
