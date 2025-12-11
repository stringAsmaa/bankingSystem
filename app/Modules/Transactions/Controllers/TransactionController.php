<?php

namespace App\Modules\Transactions\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Services\TransactionApprovalService;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected TransactionApprovalService $approvalService;

    public function __construct(TransactionApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    public function approveTransaction(Request $request, int $id)
    {
        $transaction = Transaction::find($id);
        if (!$transaction) {
            return ApiResponse::sendError('Transaction not found', 404);
        }

        $approvedTransaction = $this->approvalService->approve($transaction);

        return ApiResponse::sendResponse(
            200,
            "Transaction approved by {$approvedTransaction->approved_by}",
            $approvedTransaction
        );
    }
}
