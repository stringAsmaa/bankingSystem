<?php

namespace App\Modules\Transactions\Handlers;

use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Models\TransactionSetting;

class AdminHandler implements TransactionHandler
{
    protected ?TransactionHandler $next = null;

    public function setNext(TransactionHandler $handler): TransactionHandler
    {
        $this->next = $handler;

        return $handler;
    }

    public function handle(Transaction $transaction): Transaction
    {
        $admin = auth()->user();
        if (! $admin->hasRole('Admin')) {
            throw new \Exception('Only Admin can approve this transaction');
        }
        return $transaction->approveBy($admin->id);
    }
}
