<?php

namespace App\Modules\Transactions\Handlers;

use App\Modules\Transactions\Models\Transaction;

interface TransactionHandler
{
    //لتعيين الـ Handler التالي في السلسلة
    public function setNext(TransactionHandler $handler): TransactionHandler;
    //لمعالجة المعاملة أو تمريرها للـ Handler التالي
    public function handle(Transaction $transaction): Transaction;
}
