<?php

namespace App\Modules\administratives\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuditLogsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from'   => ['required', 'date'],
            'to'     => ['nullable', 'date', 'after_or_equal:from'],
            'export' => ['nullable', 'boolean'],
        ];
    }

    public function validatedData(): array
    {
        return [
            'from'   => $this->input('from'),
            'to'     => $this->input('to', now()->toDateString()),
            'export' => $this->boolean('export'),
        ];
    }
}
