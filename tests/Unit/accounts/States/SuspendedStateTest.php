<?php

use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Accounts\States\SuspendedState;
use App\Modules\Accounts\States\ClosedState;
use App\Modules\Accounts\States\FrozenState;
use Mockery;

beforeEach(function () {
    $this->state = new SuspendedState();

    // Partial Mock لكائن BankAccount
    $this->account = Mockery::mock(BankAccount::class)->makePartial();
    $this->account->status = 'suspended';
    $this->account->balance = 100;

    // Mock لدوال Eloquent لمنع الوصول للـ DB
    $this->account->shouldReceive('save')->andReturnTrue();
    $this->account->shouldReceive('setState')->andReturnSelf();
});

it('throws exception on deposit', function () {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Cannot deposit to a suspended account');

    $this->state->deposit($this->account, 50);
});

it('throws exception on withdraw', function () {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Cannot withdraw from a suspended account');

    $this->state->withdraw($this->account, 50);
});





it('throws exception if suspend is called again', function () {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Account is already suspended');

    $this->state->suspend($this->account);
});
