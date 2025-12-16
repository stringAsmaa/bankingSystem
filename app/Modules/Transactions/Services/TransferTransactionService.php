<?php

namespace App\Modules\Transactions\Services;

use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Enums\TransactionType;
use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Repositories\TransactionRepositoryInterface;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransferTransactionService
{
    public function __construct(
        protected TransactionRepositoryInterface $repository,
        protected TransactionRulesService $rulesService,
        protected ApplyTransactionToAccountService $applyService
    ) {}

    public function transfer(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {

            $sourceAccount = BankAccount::with('client')->findOrFail($data['source_account_id']);
            $destinationAccount = BankAccount::findOrFail($data['destination_account_id']);

            TransactionServiceHelper::ensureAccountOwnership($sourceAccount,$data['user_id']);

            $amount = $data['transaction_amount'];
            $currency = $data['transaction_currency'] ?? 'USD';

            if ($sourceAccount->balance < $amount) {
                throw new DomainException('Not enough balance to complete transfer');
            }

            $status = $this->rulesService->validate(
                $amount,
                $sourceAccount->balance,
                $currency
            );

            $transaction = $this->repository->create([
                'transaction_reference' => Str::uuid(),
                'source_account_id' => $sourceAccount->id,
                'destination_account_id' => $destinationAccount->id,
                'transaction_type' => TransactionType::TRANSFER,
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
        });
    }
}
