<?php

namespace App\Modules\Transactions\Handlers;

use App\Modules\Transactions\Models\Transaction;

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
        return $transaction->approveBy('Admin');
    }
}
