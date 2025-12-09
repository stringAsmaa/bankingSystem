<?php

namespace App\Modules\Accounts\Services;

use App\Modules\Accounts\Models\BankAccount;

class BankAccountService
{
    public function createAccount(int $clientId, array $data): BankAccount
    {
        return BankAccount::create([
            'client_id'      => $clientId,
            'account_number' => $this->generateAccountNumber(),
            'type'           => $data['type'],
            'status'         => 'active',
            'balance'        => 0,
            'currency'       => $data['currency'] ?? 'USD',
            'opened_at'      => now(),
        ]);
    }

    private function generateAccountNumber(): string
    {
        return 'AC-' . rand(10000000, 99999999);
    }

    public function update($account, array $data)
{
    $account->update($data);
    return $account;
}

public function close($account)
{
    $account->status = 'closed';
    $account->closed_at = now();
    $account->save();
    return $account;
}

}
