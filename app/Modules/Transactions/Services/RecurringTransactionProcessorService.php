<?php

namespace App\Modules\Transactions\Services;

use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Enums\TransactionFrequency;
use App\Modules\Transactions\Enums\TransactionType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RecurringTransactionProcessorService
{
    public function __construct(
        protected DepositTransactionService $depositService,
        protected WithdrawalTransactionService $withdrawalService,
        protected TransferTransactionService $transferService,
    ) {}

    public function processDueTransactions(): int
    {
        $count = 0;
        $transactions = Transaction::query()
            ->where('is_recurring', true)
            ->whereNotNull('frequency')
            ->where('next_run_at', '<=', now())
            ->get();

        foreach ($transactions as $recurring) {
            DB::transaction(function () use ($recurring, &$count) {

                //  نفذ العملية الفعلية
                $data = [
                    'source_account_id' => $recurring->source_account_id,
                    'destination_account_id' => $recurring->destination_account_id,
                    'transaction_amount' => $recurring->transaction_amount,
                    'transaction_currency' => $recurring->transaction_currency,
                    'notes' => '[AUTO] Recurring transaction',
                    'user_id' => $recurring->created_by_user_id,
                ];

                match ($recurring->transaction_type) {
                    TransactionType::DEPOSIT => $this->depositService->deposit($data),
                    TransactionType::WITHDRAWAL => $this->withdrawalService->withdraw($data),
                    TransactionType::TRANSFER => $this->transferService->transfer($data),
                };

                //  حدّث موعد التنفيذ القادم
                $recurring->next_run_at = $this->calculateNextRun($recurring);
                $recurring->save();
                $count++;
            });
        }
        return $count;
    }

    protected function calculateNextRun(Transaction $transaction): Carbon
    {
        $current = Carbon::parse($transaction->next_run_at);

        return match ($transaction->frequency) {
            TransactionFrequency::DAILY->value => $current->addDay(),
            TransactionFrequency::WEEKLY->value => $current->addWeek(),
            TransactionFrequency::MONTHLY->value => $current->addMonth(),
        };
    }
}
