<?php

namespace App\Modules\administratives\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRegistrationService
{
    /**
     * تسجيل مستخدم جديد مع تعيين الدور
     */
    public function register(array $data, string $role): User
    {
        if (! Role::where('name', $role)->exists()) {
            throw new \Exception("Role '{$role}' does not exist.");
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->assignRole($role);

        return $user;
    }
}
