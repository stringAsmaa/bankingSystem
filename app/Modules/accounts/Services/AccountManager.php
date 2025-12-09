<?php

namespace App\Modules\accounts\Services;

use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AccountManager
{
    protected UserService $userService;
    protected ClientService $clientService;
    protected BankAccountService $bankAccountService;

    public function __construct(
        UserService $userService,
        ClientService $clientService,
        BankAccountService $bankAccountService
    ) {
        $this->userService = $userService;
        $this->clientService = $clientService;
        $this->bankAccountService = $bankAccountService;
    }

    /**
     * Register user + client + bank account atomically.
     *
     * @param array $data
     * @return array ['token', 'user', 'client', 'account']
     *
     * @throws \Exception
     */
    public function registerWithAccount(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // 1. create user
            $user = $this->userService->createUser($data);

            // 2. create client
            $client = $this->clientService->createClient($user->id, $data);

            // 3. create bank account
            $account = $this->bankAccountService->createAccount($client->id, $data);

            // 4. token
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
