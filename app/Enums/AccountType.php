<?php

namespace App\Enums;

enum AccountType: string
{
    case SAVINGS = 'savings';
    case CHECKING = 'checking';
    case LOAN = 'loan';
    case INVESTMENT = 'investment';
}
