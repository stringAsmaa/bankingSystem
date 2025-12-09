<?php

namespace App\Modules\Accounts\Requests;

use App\Enums\AccountType;
use App\Enums\MaritalStatus;
use App\Enums\EmploymentStatus;
use Illuminate\Foundation\Http\FormRequest;

class StoreBankAccountRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // USER DATA
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6',

            'phone'      => 'nullable|string|max:20',
            'address'    => 'nullable|string|max:255',
            'nationality'=> 'nullable|string|max:50',
            'gender'     => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',

            // CLIENT DATA
       'employment_status' => 'required|in:' . implode(',', array_column(EmploymentStatus::cases(), 'value')),
            'marital_status'    => 'required|in:' . implode(',', array_column(MaritalStatus::cases(), 'value')),


            // BANK ACCOUNT DATA
            'type' => 'required|in:' . implode(',', array_column(AccountType::cases(), 'value')),
            'currency'  => 'required|string|in:USD,EUR,SYP',
        ];
    }
}
