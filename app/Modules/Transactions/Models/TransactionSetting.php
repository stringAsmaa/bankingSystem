<?php

namespace App\Modules\Transactions\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionSetting extends Model
{
    protected $fillable = [
        'min_amount',
        'max_amount',
        'currency',
    ];
}
