<?php

namespace App\Modules\Transactions\Contracts;

use Illuminate\Support\Collection;

interface RecommendationStrategy
{
    public function applies(Collection $transactions): bool;
    public function recommend(Collection $transactions): array;
}

