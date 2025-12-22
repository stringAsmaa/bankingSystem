<?php

namespace App\Modules\Accounts\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Accounts\Services\BankAccountStateService;
use App\Modules\Transactions\Services\TransactionServiceHelper;

class AccountStateController extends Controller
{
    protected $stateService;

    public function __construct(BankAccountStateService $stateService, protected TransactionServiceHelper $transaction)
    {
        $this->stateService = $stateService;
    }

    public function deposit(int $id, float $amount)
    {
        $account = BankAccount::find($id);
        $this->transaction->ensureAccountOwnership($account, Auth::id());
        $account = $this->stateService->deposit($id, $amount);

        return ApiResponse::sendResponse(200, 'Deposit successful', $account);
    }

    public function withdraw(int $id, float $amount)
    {
        $account = BankAccount::find($id);
        $this->transaction->ensureAccountOwnership($account, Auth::id());
        $account = $this->stateService->withdraw($id, $amount);

        return ApiResponse::sendResponse(200, 'Withdrawal successful', $account);
    }

    public function close(int $id)
    {
        if (Auth::id() !== $id) {
            return ApiResponse::sendError('You are not authorized to use this account.', 403);
        }
        $account = $this->stateService->close($id);

        return ApiResponse::sendResponse(200, 'Account closed successfully', $account);
    }

    public function freeze(int $id)
    {
        if (Auth::id() !== $id) {
            return ApiResponse::sendError('You are not authorized to use this account.', 403);
        }
        $account = $this->stateService->freeze($id);

        return ApiResponse::sendResponse(200, 'Account frozen successfully', $account);
    }

    public function suspend(int $id)
    {
        if (Auth::id() !== $id) {
            return ApiResponse::sendError('You are not authorized to use this account.', 403);
        }
        $account = $this->stateService->suspend($id);

        return ApiResponse::sendResponse(200, 'Account suspended successfully', $account);
    }
}
