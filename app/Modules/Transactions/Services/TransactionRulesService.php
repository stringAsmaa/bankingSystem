<?php

namespace App\Modules\Transactions\Services;

use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Models\TransactionSetting;
use DomainException;

class TransactionRulesService
{
    public function validate(
        float $amount,
        float $accountBalance,
        string $currency
    ): TransactionStatus {
        $settings = TransactionSetting::where('currency', $currency)->first();

        if (!$settings) {
            throw new DomainException('Transaction settings not configured.');
        }

        // أول عملية (مثلاً أول إيداع أو سحب)
        if ($accountBalance <= 0 && $amount < $settings->min_amount) {
            throw new DomainException(
                'Minimum first transaction amount is ' . $settings->min_amount
            );
        }

        // أكبر من الحد الأعلى → تحتاج موافقة
        if ($amount > $settings->max_amount) {
            return TransactionStatus::PENDING;
        }
        return TransactionStatus::COMPLETED;
    }
}
