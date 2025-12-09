<?php

namespace App\Modules\Accounts\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Accounts\Services\BankAccountService;
use App\Modules\Accounts\Requests\StoreBankAccountRequest;
use App\Modules\Accounts\Requests\UpdateBankAccountRequest;
use App\Modules\Accounts\Services\AccountRegistrationService;

class BankAccountController extends Controller
{
    protected $service;

    public function __construct(AccountRegistrationService $service,protected BankAccountService $bankService)
    {
        $this->service = $service;
    }

public function registerWithBankAccount(StoreBankAccountRequest $request)
{
    $result = $this->service->registerUserWithAccount($request->validated());

    return ApiResponse::sendResponse(
        201,
        'Client registered and bank account created successfully',
        $result
    );
}


    // public function store(StoreBankAccountRequest $request)
    // {
    //     $account = $this->service->create($request->validated());

    //     return ApiResponse::sendResponse(
    //         201,
    //         'Account created successfully',
    //         $account
    //     );
    // }

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

    public function close($id)
    {
        $account = BankAccount::find($id);

        if (! $account) {
            return ApiResponse::sendError('Account not found', 404);
        }

        $closed = $this->bankService->close($account);

        return ApiResponse::sendResponse(
            200,
            'Account closed successfully'
        );
    }
}
