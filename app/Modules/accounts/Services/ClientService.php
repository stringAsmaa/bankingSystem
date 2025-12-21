<?php

namespace App\Modules\Accounts\Services;

use App\Models\Client;
use App\Models\User;

class ClientService
{
    public function createClient(int $userId, array $data): Client
    {
        $client = Client::create([
            'user_id' => $userId,
            'customer_number' => $this->generateCustomerNumber(),
            'employment_status' => $data['employment_status'] ?? null,
            'marital_status' => $data['marital_status'] ?? null,
        ]);
        $user = User::findOrFail($userId);
        $user->assignRole('Client');
        return $client;
    }


    private function generateCustomerNumber(): string
    {
        return 'CUST-' . rand(100000, 999999);
    }
}
