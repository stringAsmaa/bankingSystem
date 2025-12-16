<?php

namespace App\Modules\Transactions\Services;

use App\Modules\Transactions\Models\Transaction;
use App\Enums\AccountStatus;
use App\Helpers\ApiResponse;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Enums\TransactionType;
use App\Modules\Accounts\Models\BankAccount;
use App\Modules\accounts\Repositories\BankAccountRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\DB;

class ApplyTransactionToAccountService
{
    public function __construct(protected BankAccountRepositoryInterface $bankRepo) {}

    public function apply(Transaction $transaction): void
    {
        if (!in_array($transaction->transaction_status, [
            TransactionStatus::COMPLETED,
            TransactionStatus::APPROVED,
        ])) {
            return;
        }

        DB::transaction(function () use ($transaction) {

            $account = BankAccount::findOrFail($transaction->source_account_id);

            if ($account->status !== AccountStatus::ACTIVE) {
                throw new DomainException('Account is not active.');
            }

            match ($transaction->transaction_type) {
                TransactionType::DEPOSIT => $this->deposit($account, $transaction),
                TransactionType::WITHDRAWAL => $this->withdraw($account, $transaction),
                TransactionType::TRANSFER => $this->transfer($transaction),
            };

            $this->bankRepo->update($account, ['balance' => $account->balance]);
        });
    }

    protected function deposit(BankAccount $account, Transaction $transaction): void
    {
        $account->balance += $transaction->transaction_amount;
    }

    protected function withdraw(BankAccount $account, Transaction $transaction): void
    {
        if ($account->balance < $transaction->transaction_amount) {
            throw new DomainException('Not enough balance to complete withdrawal');
        }

        $account->balance -= $transaction->transaction_amount;
    }

    protected function transfer(Transaction $transaction): void
    {
    }
}
