<?php

namespace App\Modules\Transactions\Enums;

enum TransactionFrequency: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
}
