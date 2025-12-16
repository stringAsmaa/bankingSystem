<?php

namespace App\Modules\Transactions\Services;

use App\Modules\Accounts\Models\BankAccount;
use DomainException;

class TransactionServiceHelper
{
    public static function ensureAccountOwnership(BankAccount $account, int $userId): void
    {
        if ($account->client->user_id !== $userId) {
            throw new DomainException('You are not authorized to use this account.');
        }
    }
}
