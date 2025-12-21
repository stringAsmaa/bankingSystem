<?php

namespace App\Modules\Transactions\Services;

use Illuminate\Support\Collection;
use App\Modules\Transactions\Strategies\RecommendationStrategy;

class RecommendationService
{
    /**
     * @param RecommendationStrategy[] $strategies
     */
    public function __construct(
        private array $strategies
    ) {}

    public function generate(Collection $transactions): array
    {
        $recommendations = [];

        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($transactions)) {
                $recommendations[] = $strategy->recommend($transactions);
            }
        }

        return $recommendations;
    }
}
