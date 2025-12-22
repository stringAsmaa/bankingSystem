<?php

namespace App\Modules\Transactions\Services;

use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Enums\TransactionType;
use App\Modules\Accounts\Models\BankAccount;
use App\Modules\accounts\Repositories\BankAccountRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\DB;

class ApplyTransactionToAccountService
{
    public function __construct(
        protected BankAccountRepositoryInterface $bankRepo
    ) {}

    public function apply(Transaction $transaction): void
    {
        if (!in_array($transaction->transaction_status, [
            TransactionStatus::COMPLETED,
            TransactionStatus::APPROVED,
        ])) {
            return;
        }

        DB::transaction(function () use ($transaction) {

            match ($transaction->transaction_type) {
                TransactionType::DEPOSIT   => $this->applyDeposit($transaction),
                TransactionType::WITHDRAWAL => $this->applyWithdrawal($transaction),
                TransactionType::TRANSFER => $this->applyTransfer($transaction),
            };
        });
    }

    protected function applyDeposit(Transaction $transaction): void
    {
        $account = BankAccount::findOrFail($transaction->source_account_id);

        // تفويض السلوك للـ State
        $account->getState()->deposit(
            $account,
            $transaction->transaction_amount
        );

        $this->bankRepo->update($account, [
            'balance' => $account->balance
        ]);
    }

    protected function applyWithdrawal(Transaction $transaction): void
    {
        $account = BankAccount::findOrFail($transaction->source_account_id);

        $account->getState()->withdraw(
            $account,
            $transaction->transaction_amount
        );

        $this->bankRepo->update($account, [
            'balance' => $account->balance
        ]);
    }

    protected function applyTransfer(Transaction $transaction): void
    {
        $source = BankAccount::findOrFail($transaction->source_account_id);
        $destination = BankAccount::findOrFail($transaction->destination_account_id);

        // سحب من المصدر حسب حالته
        $source->getState()->withdraw(
            $source,
            $transaction->transaction_amount
        );

        // إيداع في الوجهة حسب حالتها
        $destination->getState()->deposit(
            $destination,
            $transaction->transaction_amount
        );

        $this->bankRepo->update($source, [
            'balance' => $source->balance
        ]);

        $this->bankRepo->update($destination, [
            'balance' => $destination->balance
        ]);
    }
}
