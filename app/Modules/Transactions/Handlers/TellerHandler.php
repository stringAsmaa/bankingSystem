<?php

namespace App\Modules\Transactions\Handlers;

use App\Modules\Transactions\Models\Transaction;

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
        if ($transaction->amount <= 5000) {
            return $transaction->approveBy('Teller');
        }

        if ($this->next) {
            return $this->next->handle($transaction);
        }

        throw new \Exception("Transaction requires higher approval");
    }
}
