<?php

namespace App\Modules\Transactions\Handlers;

use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Models\TransactionSetting;

class ManagerHandler implements TransactionHandler
{
    protected ?TransactionHandler $next = null;

    public function setNext(TransactionHandler $handler): TransactionHandler
    {
        $this->next = $handler;

        return $handler;
    }

    public function handle(Transaction $transaction): Transaction
    {
        $manager = auth()->user();
        $setting = TransactionSetting::where('currency', $transaction->currency)->first();
        if ($transaction->transaction_amount >= 2 * ($setting->max_amount) && $transaction->transaction_amount <= 3 * ($setting->max_amount)) {
            if (! $manager->hasRole('Manager')) {
                throw new \Exception('Only Manager can approve this transaction');
            }
            return $transaction->approveBy($manager->id);
        }

        if ($this->next) {
            return $this->next->handle($transaction);
        }

        throw new \Exception('Transaction requires Admin approval');
    }
}
