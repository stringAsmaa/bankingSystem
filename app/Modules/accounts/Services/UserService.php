<?php

namespace App\Modules\Accounts\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(array $data): User
    {
        return User::create([
            'name'        => $data['name'],
            'email'       => $data['email'],
            'password'    => Hash::make($data['password']),
            'phone'       => $data['phone'] ?? null,
            'address'     => $data['address'] ?? null,
            'nationality' => $data['nationality'] ?? null,
            'gender'      => $data['gender'] ?? null,
            'birth_date'  => $data['birth_date'] ?? null,
            'is_active'   => true,
        ]);
    }
}
