<?php

namespace App\Modules\Transactions\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Transactions\Enums\TransactionFrequency;
use App\Modules\Transactions\Enums\TransactionType;
use App\Modules\Transactions\Enums\TransactionStatus;

class Transaction extends Model
{
    use LogsActivity;

    protected $table = 'transactions';

    protected $fillable = [
        'transaction_reference',
        'source_account_id',
        'destination_account_id',
        'transaction_type',
        'transaction_status',
        'transaction_amount',
        'transaction_currency',
        'notes',
        'metadata',
        'created_by_user_id',
        'approved_by_user_id',
        'approved_at',
        'completed_at',
        'is_recurring',
        'next_run_at',
        'frequency',
    ];

    protected $casts = [
        'metadata' => 'array',
        'transaction_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'transaction_type' => TransactionType::class,
        'transaction_status' => TransactionStatus::class,
        'frequency' => TransactionFrequency::class,
        'next_run_at' => 'datetime',
    ];

public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logOnly(['transaction_amount', 'transaction_status'])
        ->useLogName('transactions')
        ->logOnlyDirty();
}


    public function sourceAccount()
    {
        return $this->belongsTo(BankAccount::class, 'source_account_id');
    }

    public function destinationAccount()
    {
        return $this->belongsTo(BankAccount::class, 'destination_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function approveBy(string $role): self
    {
        $this->transaction_status = 'approved';
        $this->approved_by_user_id = $role;
        $this->save();

        return $this;
    }

    public function reject(string $reason): self
    {
        $this->transaction_status = 'rejected';
        $this->approved_by_user_id = $reason;
        $this->save();

        return $this;
    }
}
