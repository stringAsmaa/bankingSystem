<?php

namespace App\Modules\administratives\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AccountSummariesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'export' => ['nullable', 'boolean'],
        ];
    }

    public function export(): bool
    {
        return $this->boolean('export');
    }
}
