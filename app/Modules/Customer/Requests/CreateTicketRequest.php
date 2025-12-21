<?php

namespace App\Modules\Customer\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Customer\Enums\TicketPriority;

class CreateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'nullable|in:' . implode(',', array_column(TicketPriority::cases(), 'value')),
        ];
    }
}
