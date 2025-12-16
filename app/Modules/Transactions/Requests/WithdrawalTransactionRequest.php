<?php

namespace App\Modules\Transactions\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawalTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // تأكد من أن المستخدم مسموح له بالقيام بسحب
        return true;
    }

    public function rules(): array
    {
        return [
            'source_account_id' => 'required|exists:bank_accounts,id',
            'transaction_amount' => 'required|numeric|min:0.01',
            'transaction_currency' => 'nullable|string|size:3',
            'notes' => 'nullable|string|max:255',
        ];
    }
}
