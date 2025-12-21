<?php

namespace App\Modules\Accounts\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\Accounts\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_token' => 'nullable|string',
        ]);

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
