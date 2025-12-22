<?php

namespace App\Modules\administratives\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\registerRequest;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Modules\administratives\Services\UserRegistrationService;

class UserRegistrationController extends Controller
{
    protected UserRegistrationService $registrationService;

    public function __construct(UserRegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    /**
     * تسجيل مستخدم جديد لأي رول: teller, manager, admin
     */
    public function register(registerRequest $request)
    {
        try {
            $user = $this->registrationService->register(
                $request->only('name', 'email', 'password'),
                $request->role
            );
            $token = JWTAuth::fromUser($user);

            return ApiResponse::sendResponse(201, "User registered successfully with role {$request->role}",[ $user,$token]);
        } catch (\Exception $e) {
            return ApiResponse::sendError($e->getMessage(), 400);
        }}
}
