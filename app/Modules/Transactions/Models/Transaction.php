<?php

namespace App\Modules\Transactions\Models;

use App\Models\User;
use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Transactions\Enums\TransactionStatus;
use App\Modules\Transactions\Enums\TransactionType;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
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
    ];

    protected $casts = [
        'metadata' => 'array',
        'transaction_amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
        'transaction_type' => TransactionType::class,
        'transaction_status' => TransactionStatus::class,
    ];

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
    /**
     * تعيين الموافقة على المعاملة
     */
    public function approveBy(string $role): self
    {
        $this->status = 'approved';
        $this->approved_by = $role;
        $this->save();
        return $this;
    }

    /**
     * رفض المعاملة
     */
    public function reject(string $reason): self
    {
        $this->status = 'rejected';
        $this->approved_by = $reason;
        $this->save();
        return $this;
    }
}
