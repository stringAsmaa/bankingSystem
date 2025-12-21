<?php

namespace App\Modules\Transactions\Requests;

use App\Modules\Transactions\Models\TransactionSetting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateTransactionSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $setting = TransactionSetting::first();

        return [
            'min_amount' => ['sometimes','numeric','min:0',],
            'max_amount' => ['sometimes','numeric',Rule::when(
                    $this->has('min_amount'),
                    'gte:min_amount',
                    'gte:' . ($setting?->min_amount ?? 0)),
            ],
            'currency' => 'sometimes|string|size:3',
        ];
    }
}
