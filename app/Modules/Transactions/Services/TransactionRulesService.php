<?php

namespace App\Modules\Transactions\Services;

use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Models\TransactionSetting;
use RuntimeException;

class TransactionRulesService
{
    public function validate(float $amount,float $accountBalance,string $currency): TransactionStatus
    {
        $settings = TransactionSetting::query()->where('currency', $currency)->first();

        if (!$settings) {
            throw new RuntimeException('Transaction settings not configured');
        }
        // أول إيداع
        if ($accountBalance <= 0 && $amount < $settings->min_amount) {
            throw new RuntimeException(
                'Minimum first deposit is ' . $settings->min_amount
            );
        }
        // أكبر من الحد الأعلى
        if ($amount > $settings->max_amount) {
            return TransactionStatus::PENDING;
        }
        return TransactionStatus::PENDING;
    }
}
