<?php

namespace App\Modules\Transactions\Controllers;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Modules\Transactions\Requests\CreateRecurringTransactionRequest;
use App\Modules\Transactions\Resources\TransactionResource;
use App\Modules\Transactions\Services\RecurringTransactionService;

class RecurringTransactionController extends Controller
{
    public function __construct(
        protected RecurringTransactionService $service
    ) {}

    public function store(CreateRecurringTransactionRequest $request)
    {
        $transaction = $this->service->create(
            $request->validated() + [
                'user_id' => $request->user()->id
            ]
        );

        return ApiResponse::sendResponse(
            201,
            'Recurring transaction scheduled successfully',
            new TransactionResource($transaction)
        );
    }
}
