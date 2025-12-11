<?php

namespace App\Modules\Accounts\Models;

use App\Models\Client;
use App\Enums\AccountType;
use App\Enums\AccountStatus;
use Illuminate\Database\Eloquent\Model;
use App\Modules\accounts\States\ActiveState;
use App\Modules\accounts\States\AccountState;

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
//لحتى نحول انواع الحقول تلقائيا عند القراءة و الكتابة
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

    public function setStateByStatus()
    {
        $this->state = match ($this->status) {
            AccountStatus::ACTIVE->value => new ActiveState(),
            AccountStatus::FROZEN->value => new \App\Modules\Accounts\States\FrozenState(),
            AccountStatus::SUSPENDED->value => new \App\Modules\Accounts\States\SuspendedState(),
            AccountStatus::CLOSED->value => new \App\Modules\Accounts\States\ClosedState(),
            default => new ActiveState(),
        };
    }

    public function deposit(float $amount) { return $this->state->deposit($this, $amount); }
    public function withdraw(float $amount) { return $this->state->withdraw($this, $amount); }
    public function close() { return $this->state->close($this); }
    public function freeze() { return $this->state->freeze($this); }
    public function suspend() { return $this->state->suspend($this); }
}
