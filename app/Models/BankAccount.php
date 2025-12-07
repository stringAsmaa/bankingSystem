<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'customer_id',
        'account_number',
        'type',
        'status',
        'balance',
        'currency',
        'opened_at',
        'closed_at',
    ];

    protected $casts = [
        'type' => AccountType::class,
        'status' => AccountStatus::class,
        'balance' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }
}
