<?php

namespace App\Modules\Transactions\Observers;

use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Models\TransactionSetting;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Services\NotificationService;

class TransactionObserver
{
    public function created(Transaction $transaction): void
    {
        $this->notifyIfLarge($transaction);
    }

    public function updated(Transaction $transaction): void
    {
        if ($transaction->wasChanged('transaction_status')) {
            $this->notifyStatusChange($transaction);
        }
    }

    protected function notifyIfLarge(Transaction $transaction): void
    {
        $settings = TransactionSetting::where(
            'currency',
            $transaction->transaction_currency
        )->first();

        if (!$settings || $transaction->transaction_amount <= $settings->max_amount) {
            return;
        }

        $userId = $transaction->sourceAccount?->client?->user_id;

        /** @var NotificationService $notifier */
        $notifier = app(NotificationService::class);

        // إشعار المستخدم
        $notifier->sendToUser(
            $userId,
            'Large Transaction Pending Approval',
            "Your {$transaction->transaction_type->value} of {$transaction->transaction_amount} requires admin approval.",
            [
                'transaction_id' => $transaction->id,
                'amount' => (string) $transaction->transaction_amount,
                'status' => TransactionStatus::PENDING->value,
            ]
        );

        // إشعار الإدارة
        $notifier->sendToRole(
            'Admin',
            'Large Transaction Alert',
            "Transaction #{$transaction->id} requires approval.",
            [
                'transaction_id' => $transaction->id,
                'amount' => (string) $transaction->transaction_amount,
                'type' => $transaction->transaction_type->value,
            ]
        );
    }

    protected function notifyStatusChange(Transaction $transaction): void
    {
        $transaction->loadMissing('sourceAccount.client');

        $userId = $transaction->sourceAccount?->client?->user_id;

        /** @var NotificationService $notifier */
        $notifier = app(NotificationService::class);

        $notifier->sendToUser(
            $userId,
            'Transaction Status Updated',
            "Your {$transaction->transaction_type->value} is now {$transaction->transaction_status->value}.",
            [
                'transaction_id' => $transaction->id,
                'status' => $transaction->transaction_status->value,
            ]
        );
    }
}
