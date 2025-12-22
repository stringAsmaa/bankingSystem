<?php

namespace App\Modules\Accounts\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Accounts\Facades\AccountFacade;
use App\Modules\Accounts\Services\BankAccountService;
use App\Modules\Accounts\Requests\StoreBankAccountRequest;
use App\Modules\Accounts\Requests\UpdateBankAccountRequest;

class BankAccountController extends Controller
{
    public function __construct(protected BankAccountService $bankService) {}

    public function registerWithBankAccount(StoreBankAccountRequest $request)
    {
        $result = AccountFacade::registerWithAccount($request->validated());

        return ApiResponse::sendResponse(
            201,
            'Client registered and bank account created successfully',
            $result
        );
    }

    public function update(UpdateBankAccountRequest $request, $id)
    {
        $account = BankAccount::find($id);

        if (! $account) {
            return ApiResponse::sendError('Account not found', 404);
        }

        $updated = $this->bankService->update($account, $request->validated());

        return ApiResponse::sendResponse(
            200,
            'Account updated successfully',
            $updated
        );
    }

//     public function close($id)
//     {
//         $account = BankAccount::find($id);

//         if (! $account) {
//             return ApiResponse::sendError('Account not found', 404);
//         }
//             $user = Auth::user();
// // dd($account->client_id , $user->clients->id);
//     if ($user->hasRole('Client')) {
//         if ($account->client_id !== $user->clients->id) {
//             return ApiResponse::sendError('Unauthorized action', 403);
//         }
//     }
//         $closed = $this->bankService->close($account);

//         return ApiResponse::sendResponse(
//             200,
//             'Account closed successfully'
//         );
//     }
}
