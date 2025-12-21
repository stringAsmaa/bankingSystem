<?php

namespace App\Modules\Transactions\Strategies;

use Illuminate\Support\Collection;
use App\Modules\Transactions\Contracts\RecommendationStrategy;

class StableActivityStrategy implements RecommendationStrategy
{
    public function applies(Collection $transactions): bool
    {
        return $transactions->count() >= 10;
    }

    public function recommend(Collection $transactions): array
    {
        return [
            'title' => 'Stable Financial Activity',
            'message' => 'Your account shows stable activity. You may benefit from long-term savings or investment options.',
            'priority' => 'low',
        ];
    }
}
