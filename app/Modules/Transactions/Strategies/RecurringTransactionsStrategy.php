<?php

namespace App\Modules\Transactions\Strategies;

use Illuminate\Support\Collection;
use App\Modules\Transactions\Contracts\RecommendationStrategy;

class RecurringTransactionsStrategy implements RecommendationStrategy
{
    public function applies(Collection $transactions): bool
    {
        return $transactions->where('is_recurring', true)->isNotEmpty();
    }

    public function recommend(Collection $transactions): array
    {
        return [
            'title' => 'Recurring Payments Detected',
            'message' => 'You have recurring transactions. Reviewing subscriptions could help reduce costs.',
            'priority' => 'medium',
        ];
    }
}
