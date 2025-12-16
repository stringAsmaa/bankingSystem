<?php

namespace App\Modules\Transactions\Services;

use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Enums\TransactionType;
use App\Modules\Transactions\Integrations\PaymentGateway;
use App\Modules\Transactions\Repositories\TransactionRepositoryInterface;
use Illuminate\Support\Str;

class DepositTransactionService
{
    public function __construct(
        protected TransactionRepositoryInterface $repository,
        protected PaymentGateway $paymentGateway,
        protected TransactionRulesService $rulesService

    ) {}

    public function deposit(array $data)
    {
        $account = BankAccount::findOrFail($data['source_account_id']);
        $currency = $data['transaction_currency'] ?? 'USD';

        $status = $this->rulesService->validate(
            $data['transaction_amount'],
            $account->balance,
            $currency
        );
        $transaction = $this->repository->create([
            'transaction_reference' => Str::uuid(),
            'source_account_id' => $data['source_account_id'],
            'transaction_type' => TransactionType::DEPOSIT,
            'transaction_amount' => $data['transaction_amount'],
            'transaction_currency' => $currency,
            'transaction_status' => $status,
            'notes' => $data['notes'] ?? null,
            'created_by_user_id' => $data['user_id'],
        ]);

        $checkout = $this->paymentGateway->createCheckout([
            'transaction_id' => $transaction->id,
            'amount' => $transaction->transaction_amount,
            'currency' => $transaction->transaction_currency,
            'description' => 'Deposit #'.$transaction->transaction_reference,
        ]);
        $transaction->metadata = [
            'checkout_url' => $checkout['checkout_url'],
            'checkout_session_id' => $checkout['session_id'],
        ];
        $this->repository->save($transaction);

        return $transaction;
    }
}
