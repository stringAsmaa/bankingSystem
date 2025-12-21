<?php

namespace App\Modules\Transactions\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Services\TransactionApprovalService;
use Illuminate\Http\Request;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Services\ApplyTransactionToAccountService;

class TransactionController extends Controller
{
    protected TransactionApprovalService $approvalService;
    protected ApplyTransactionToAccountService $applyService;

    public function __construct(
        TransactionApprovalService $approvalService,
        ApplyTransactionToAccountService $applyService
    ) {
        $this->approvalService = $approvalService;
        $this->applyService = $applyService;
    }

    public function approveTransaction(Request $request, int $id)
    {
        $transaction = Transaction::find($id);
        if (! $transaction) {
            return ApiResponse::sendError('Transaction not found', 404);
        }
        if ($transaction->transaction_status == TransactionStatus::APPROVED) {
            return ApiResponse::sendError('this transaction already approved');
        }
        $approvedTransaction = $this->approvalService->approve($transaction);
        $this->applyService->apply($approvedTransaction);
        return ApiResponse::sendResponse(
            200,
            "Transaction approved by {$approvedTransaction->approved_by_user_id}",
            $approvedTransaction
        );
    }
}
