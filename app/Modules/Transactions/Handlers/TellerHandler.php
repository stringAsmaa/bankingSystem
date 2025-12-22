<?php

namespace App\Modules\Transactions\Handlers;

use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Models\TransactionSetting;

class TellerHandler implements TransactionHandler
{
    protected ?TransactionHandler $next = null;

    public function setNext(TransactionHandler $handler): TransactionHandler
    {
        $this->next = $handler;

        return $handler;
    }

    public function handle(Transaction $transaction): Transaction
    {
        $teller = auth()->user();
        $setting = TransactionSetting::where('currency', $transaction->transaction_currency)->first();
        if ($transaction->transaction_amount <= 2 * ($setting->max_amount) && $transaction->transaction_amount >= $setting->max_amount) {
            if (! $teller->hasRole('Teller')) {
                throw new \Exception('Only Teller can approve this transaction');
            }
            return $transaction->approveBy($teller->id);
        }

        if ($this->next) {
            return $this->next->handle($transaction);
        }

        throw new \Exception('Transaction requires higher approval');
    }
}
