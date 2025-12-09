<?php

namespace App\Modules\Accounts\Models;

use App\Models\Client;
use App\Enums\AccountType;
use App\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
 protected $fillable = [
        'client_id',
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
    }}
