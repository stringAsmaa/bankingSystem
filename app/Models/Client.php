<?php

namespace App\Models;

use App\Enums\EmploymentStatus;
use App\Enums\MaritalStatus;
use App\Modules\Accounts\Models\BankAccount;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'user_id',
        'customer_number',
        'employment_status',
        'marital_status',
    ];

    protected $casts = [
        'employment_status' => EmploymentStatus::class,
        'marital_status' => MaritalStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class, 'client_id');
    }
}
