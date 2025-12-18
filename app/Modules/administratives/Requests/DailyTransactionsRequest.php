<?php

namespace App\Modules\administratives\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DailyTransactionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'   => ['nullable', 'date'],
            'export' => ['nullable', 'boolean'],
        ];
    }

    public function validatedData(): array
    {
        return [
            'date'   => $this->input('date', now()->toDateString()),
            'export' => $this->boolean('export'),
        ];
    }
}
