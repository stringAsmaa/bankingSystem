<?php

namespace App\Modules\Accounts\Observers;

use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Transactions\Services\NotificationService;
use App\Modules\accounts\Jobs\SendBalanceUpdatedNotification;

class BankAccountObserver
{
    public function updated(BankAccount $account): void
    {
        if (!$account->wasChanged('balance')) {
            return;
        }

        // $userId = $account->client->user_id;

        // $oldBalance = $account->getOriginal('balance');
        // $newBalance = $account->balance;

        // /** @var NotificationService $notifier */
        // $notifier = app(NotificationService::class);

        // $notifier->sendToUser(
        //     $userId,
        //     'Balance Updated',
        //     "Your balance changed from {$oldBalance} to {$newBalance}.",
        //     [
        //         'old_balance' => (string) $oldBalance,
        //         'new_balance' => (string) $newBalance,
        //         'account_id' => $account->id,
        //     ]
        // );


            SendBalanceUpdatedNotification::dispatch(
            $account->client->user_id,
            $account->getOriginal('balance'),
            $account->balance,
            $account->id
        );
    }

}
