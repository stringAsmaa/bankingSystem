<?php

namespace App\Modules\Transactions\Strategies;

use App\Modules\Transactions\Contracts\RecommendationStrategy;
use App\Modules\Transactions\Enums\TransactionType;
use Illuminate\Support\Collection;

class HighSpendingStrategy implements RecommendationStrategy
{
    public function applies(Collection $transactions): bool
    {
        $total = $transactions
            ->whereIn('transaction_type', [TransactionType::WITHDRAWAL->value,TransactionType::TRANSFER->value])
            ->sum('transaction_amount');

        return $total > 5000;
    }

    public function recommend(Collection $transactions): array
    {
        return [
            'title' => 'High Spending Alert',
            'message' => 'Your outgoing transactions are higher than usual. Consider setting spending limits.',
            'priority' => 'high',
        ];
    }
}
