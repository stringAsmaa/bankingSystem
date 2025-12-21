<?php

namespace App\Modules\Accounts\Controllers;

use App\Models\User;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Accounts\Models\DeviceToken;
use App\Modules\accounts\Requests\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $data = $request->validated();

        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        if (! $token = Auth::guard('api')->attempt($credentials)) {
            return ApiResponse::sendError('Invalid credentials', 401);
        }
        $user = Auth::guard('api')->user()
            ?? User::where('email', $data['email'])->first();
        if (!empty($data['device_token'])) {
            DeviceToken::updateOrCreate(
                ['user_id' => $user->id],
                ['device_token' => $data['device_token']]
            );
        }
        return ApiResponse::sendResponse(200, 'Login successful', [
            'access_token' => $token,
        ]);
    }
}
