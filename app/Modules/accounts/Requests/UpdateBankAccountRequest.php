<?php

namespace App\Modules\Accounts\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\AccountStatus;
use App\Enums\AccountType;

class UpdateBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

 public function rules(): array
{
    return [
        'account_number' => 'sometimes|string|unique:bank_accounts,account_number,' . $this->route('id'),
        'type' => 'sometimes|in:' . implode(',', array_map(fn($case) => $case->value, \App\Enums\AccountType::cases())),
        'status' => 'sometimes|in:' . implode(',', array_map(fn($case) => $case->value, \App\Enums\AccountStatus::cases())),
        'balance' => 'sometimes|numeric|min:0',
        'currency' => 'sometimes|string|size:3',
    ];
}

}
