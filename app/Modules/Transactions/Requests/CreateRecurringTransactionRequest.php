<?php

namespace App\Modules\Transactions\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Transactions\Enums\TransactionType;
use App\Modules\Transactions\Enums\TransactionFrequency;

class CreateRecurringTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'source_account_id' => 'required|exists:bank_accounts,id',
            'destination_account_id' => 'nullable|exists:bank_accounts,id',
            'transaction_type' => 'required|in:' . implode(',', array_column(TransactionType::cases(), 'value')),
            'transaction_amount' => 'required|numeric|min:0.01',
            'transaction_currency' => 'nullable|string|size:3',
            'notes' => 'nullable|string',

            // recurring
            'start_at' => 'required|date',
            'frequency' => 'required|in:' . implode(',', array_column(TransactionFrequency::cases(), 'value')),
        ];
    }
}
