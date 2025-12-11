<?php

namespace App\Modules\Transactions\Services;

use App\Modules\Transactions\Handlers\TellerHandler;
use App\Modules\Transactions\Handlers\ManagerHandler;
use App\Modules\Transactions\Handlers\AdminHandler;
use App\Modules\Transactions\Models\Transaction;

class TransactionApprovalService
{
    protected  $chain;

    public function __construct()
    {
        // بناء سلسلة الموافقة
        $teller = new TellerHandler();
        $manager = new ManagerHandler();
        $admin = new AdminHandler();

        $teller->setNext($manager)->setNext($admin);
        $this->chain = $teller;
    }

    public function approve(Transaction $transaction): Transaction
    {
        return $this->chain->handle($transaction);
    }
}
