<?php

use App\Modules\accounts\Models\BankAccount;
use App\Modules\accounts\States\ActiveState;
use App\Modules\accounts\States\ClosedState;
use App\Modules\accounts\States\FrozenState;
use App\Modules\accounts\States\SuspendedState;

/*
|--------------------------------------------------------------------------
| Dummy BankAccount (Eloquent-safe Test Double)
|--------------------------------------------------------------------------
*/
class DummyBankAccount extends BankAccount
{
    public float $balance = 0;
    public string $status = 'active';
    public $closed_at = null;
    public $state = null;

    public bool $saved = false;

    // تعطيل Eloquent behavior
    public function save(array $options = [])
    {
        $this->saved = true;
        return true;
    }

    // تعطيل setAttribute الخاص بـ Eloquent
    public function setAttribute($key, $value)
    {
        $this->$key = $value;
        return $this;
    }

    public function setState($state)
    {
        $this->state = $state;
    }
}

/*
|--------------------------------------------------------------------------
| ActiveState – Pure Isolated Unit Tests
|--------------------------------------------------------------------------
*/

it('deposits money and saves account', function () {
    $account = new DummyBankAccount();
    $account->balance = 100;

    $state = new ActiveState();

    $result = $state->deposit($account, 50);

    expect($account->balance)->toBe(150.0)
        ->and($account->saved)->toBeTrue()
        ->and($result)->toBe($account);
});

it('withdraws money when balance is sufficient', function () {
    $account = new DummyBankAccount();
    $account->balance = 200;

    $state = new ActiveState();

    $result = $state->withdraw($account, 80);

    expect($account->balance)->toBe(120.0)
        ->and($account->saved)->toBeTrue()
        ->and($result)->toBe($account);
});

it('throws exception when withdrawing more than balance', function () {
    $account = new DummyBankAccount();
    $account->balance = 50;

    $state = new ActiveState();

    expect(fn () => $state->withdraw($account, 100))
        ->toThrow(Exception::class, 'Insufficient balance');
});

it('closes active account and sets closed state', function () {
    $account = new DummyBankAccount();

    $state = new ActiveState();

    $result = $state->close($account);

    expect($account->status)->toBe('closed')
        ->and($account->closed_at)->not->toBeNull()
        ->and($account->state)->toBeInstanceOf(ClosedState::class)
        ->and($account->saved)->toBeTrue()
        ->and($result)->toBe($account);
});

it('freezes active account and sets frozen state', function () {
    $account = new DummyBankAccount();

    $state = new ActiveState();

    $result = $state->freeze($account);

    expect($account->status)->toBe('frozen')
        ->and($account->state)->toBeInstanceOf(FrozenState::class)
        ->and($account->saved)->toBeTrue()
        ->and($result)->toBe($account);
});

it('suspends active account and sets suspended state', function () {
    $account = new DummyBankAccount();

    $state = new ActiveState();

    $result = $state->suspend($account);

    expect($account->status)->toBe('suspended')
        ->and($account->state)->toBeInstanceOf(SuspendedState::class)
        ->and($account->saved)->toBeTrue()
        ->and($result)->toBe($account);
});