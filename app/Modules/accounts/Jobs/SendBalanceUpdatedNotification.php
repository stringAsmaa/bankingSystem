<?php

namespace App\Modules\accounts\Jobs;

use App\Modules\Transactions\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBalanceUpdatedNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $userId,
        public float $oldBalance,
        public float $newBalance,
        public int $accountId
    ) {}

    public function handle(NotificationService $notifier): void
    {
        $notifier->sendToUser(
            $this->userId,
            'Balance Updated',
            "Your balance changed from {$this->oldBalance} to {$this->newBalance}.",
            [
                'old_balance' => (string) $this->oldBalance,
                'new_balance' => (string) $this->newBalance,
                'account_id' => $this->accountId,
            ]
        );
    }
}
