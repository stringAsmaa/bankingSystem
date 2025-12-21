<?php

namespace App\Modules\Transactions\Services;

use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Enums\TransactionStatus;
use Illuminate\Support\Str;

class RecurringTransactionService
{
    public function create(array $data): Transaction
    {
        return Transaction::create([
            'transaction_reference' => Str::uuid(),
            'source_account_id' => $data['source_account_id'],
            'destination_account_id' => $data['destination_account_id'] ?? null,
            'transaction_type' => $data['transaction_type'],
            'transaction_amount' => $data['transaction_amount'],
            'transaction_currency' => $data['transaction_currency'] ?? 'USD',
            'notes' => $data['notes'] ?? null,
            'transaction_status' => TransactionStatus::PENDING,

            'is_recurring' => true,
            'frequency' => $data['frequency'],
            'next_run_at' => $data['start_at'],

            'created_by_user_id' => $data['user_id'],
        ]);
    }
}
