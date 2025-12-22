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
        $user = Auth::user();
        $account = BankAccount::find($id);

        if ($user->hasRole('Client')) {

            if (! $user->clients) {
                return ApiResponse::sendError('Client profile not found', 403);
            }
            if ($account->client_id !== $user->clients->id) {
                return ApiResponse::sendError('Unauthorized action', 403);
            }
        }

        $account = $this->stateService->close($id);

        return ApiResponse::sendResponse(200, 'Account closed successfully', $account);
    }

    public function freeze(int $id)
    {
        $user = Auth::user();
        $account = BankAccount::find($id);

        if ($user->hasRole('Client')) {

            if (! $user->clients) {
                return ApiResponse::sendError('Client profile not found', 403);
            }
            if ($account->client_id !== $user->clients->id) {
                return ApiResponse::sendError('Unauthorized action', 403);
            }
        }
        $account = $this->stateService->freeze($id);

        return ApiResponse::sendResponse(200, 'Account frozen successfully', $account);
    }

    public function suspend(int $id)
    {
        $user = Auth::user();
        $account = BankAccount::find($id);

        if ($user->hasRole('Client')) {

            if (! $user->clients) {
                return ApiResponse::sendError('Client profile not found', 403);
            }
            if ($account->client_id !== $user->clients->id) {
                return ApiResponse::sendError('Unauthorized action', 403);
            }
        }
        $account = $this->stateService->suspend($id);

        return ApiResponse::sendResponse(200, 'Account suspended successfully', $account);
    }
}
