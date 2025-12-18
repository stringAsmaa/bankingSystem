<?php

namespace App\Modules\Accounts\Models;

use App\Models\Client;
use App\Enums\AccountType;
use App\Enums\AccountStatus;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Modules\accounts\States\ActiveState;
use App\Modules\accounts\States\AccountState;
use App\Modules\Transactions\Models\Transaction;
use App\Modules\Accounts\Services\BankAccountService;

class BankAccount extends Model
{
        use LogsActivity;

    protected $fillable = [
        'client_id',
        'account_number',
        'type',
        'status',
        'balance',
        'currency',
        'opened_at',
        'closed_at',
        'parent_id',
    ];

    // لحتى نحول انواع الحقول تلقائيا عند القراءة و الكتابة
    protected $casts = [
        'type' => AccountType::class,
        'status' => AccountStatus::class,
        'balance' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
{
    return LogOptions::defaults()
        ->logOnly(['type', 'status'])
        ->useLogName('bankAccounts')
        ->logOnlyDirty();
}
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    protected $state;

    /*
    retrieved()

هاد حدث يأتي من Eloquent ويُطلق كل مرة يتم فيها جلب سجل من قاعدة البيانات.
*/
    protected static function booted()
    {
        static::retrieved(function ($account) {
            $account->setStateByStatus();
        });
    }

    public function setState(AccountState $state)
    {
        $this->state = $state;
    }

    public function getState(): AccountState
    {
        // إذا لم تكن الحالة موجودة، نعين الحالة الافتراضية
        if (! isset($this->state)) {
            $this->setStateByStatus();
        }

        return $this->state;
    }

    public function setStateByStatus()
    {
        $this->state = match ($this->status) {
            AccountStatus::ACTIVE->value => new ActiveState,
            AccountStatus::FROZEN->value => new \App\Modules\Accounts\States\FrozenState,
            AccountStatus::SUSPENDED->value => new \App\Modules\Accounts\States\SuspendedState,
            AccountStatus::CLOSED->value => new \App\Modules\Accounts\States\ClosedState,
            default => new ActiveState,
        };
    }

    public function deposit(float $amount)
    {
        return $this->state->deposit($this, $amount);
    }

    public function withdraw(float $amount)
    {
        return $this->state->withdraw($this, $amount);
    }

    public function close()
    {
        return $this->state->close($this);
    }

    public function freeze()
    {
        return $this->state->freeze($this);
    }

    public function suspend()
    {
        return $this->state->suspend($this);
    }

    // العلاقة مع الأب
    public function parent()
    {
        return $this->belongsTo(BankAccount::class, 'parent_id');
    }

    // العلاقة مع الأبناء
    public function children()
    {
        return $this->hasMany(BankAccount::class, 'parent_id');
    }

    // حساب الرصيد الكلي لحساب رئيسي مع كل الأبناء
    public function totalBalance(): float
    {
        $total = $this->balance;
        foreach ($this->children as $child) {
            $total += $child->totalBalance();
        }

        return $total;
    }

    // إضافة حساب فرعي
    public function addChild(array $data): BankAccount
    {
        $data['parent_id'] = $this->id;

        return app(BankAccountService::class)->createAccount($this->client_id, $data);
    }
      public function transactions()
    {
        return $this->hasMany(Transaction::class, 'source_account_id');
    }
}
