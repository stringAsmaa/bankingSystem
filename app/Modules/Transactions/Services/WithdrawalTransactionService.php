<?php

namespace App\Modules\Transactions\Services;

use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Enums\TransactionType;
use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Repositories\TransactionRepositoryInterface;
use Illuminate\Support\Str;
use DomainException;

class WithdrawalTransactionService
{
    public function __construct(
        protected TransactionRepositoryInterface $repository,
        protected TransactionRulesService $rulesService,
        protected ApplyTransactionToAccountService $applyService
    ) {}

    public function withdraw(array $data): Transaction
    {
        $account = BankAccount::findOrFail($data['source_account_id']);
        $currency = $data['transaction_currency'] ?? 'USD';
        $amount = $data['transaction_amount'];

        $status = $this->rulesService->validate($amount, $account->balance, $currency);

        if ($status === TransactionStatus::FAILED || $amount > $account->balance) {
            throw new DomainException('Not enough balance to complete withdrawal.');
        }

        $transaction = $this->repository->create([
            'transaction_reference' => Str::uuid(),
            'source_account_id' => $account->id,
            'transaction_type' => TransactionType::WITHDRAWAL,
            'transaction_amount' => $amount,
            'transaction_currency' => $currency,
            'transaction_status' => $status,
            'notes' => $data['notes'] ?? null,
            'created_by_user_id' => $data['user_id'],
        ]);

        if ($status === TransactionStatus::COMPLETED) {
            $this->applyService->apply($transaction);
        }
        return $transaction;
    }
}
