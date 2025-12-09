<?php

namespace App\Modules\Accounts\Services;

use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AccountRegistrationService
{
    protected $userService;
    protected $clientService;
    protected $bankAccountService;

    public function __construct(
        UserService $userService,
        ClientService $clientService,
        BankAccountService $bankAccountService
    ) {
        $this->userService = $userService;
        $this->clientService = $clientService;
        $this->bankAccountService = $bankAccountService;
    }

    public function registerUserWithAccount(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // 1. Create User
            $user = $this->userService->createUser($data);

            // 2. Create Client
            $client = $this->clientService->createClient($user->id, $data);

            // 3. Create Bank Account
            $account = $this->bankAccountService->createAccount($client->id, $data);

            // 4. Generate JWT
            $token = JWTAuth::fromUser($user);

            return [
                'token'   => $token,
                'user'    => $user,
                'client'  => $client,
                'account' => $account,
            ];
        });
    }
}
