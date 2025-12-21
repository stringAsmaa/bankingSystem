<?php

use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Accounts\States\FrozenState;
use App\Modules\Accounts\States\ClosedState;
use App\Modules\Accounts\States\SuspendedState;
use Mockery;
use App\Enums\AccountStatus;

beforeEach(function () {
    $this->state = new FrozenState();

    // Partial Mock يسمح بتعيين الخصائص
    $this->account = Mockery::mock(BankAccount::class)->makePartial();
    $this->account->status = AccountStatus::FROZEN; // أو 'frozen' إذا نص
    $this->account->balance = 100;

    $this->account->shouldReceive('save')->andReturnTrue();
    $this->account->shouldReceive('setState')->andReturnSelf();
});

it('throws exception on deposit', function () {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Cannot deposit to a frozen account');

    $this->state->deposit($this->account, 50);
});

it('throws exception on withdraw', function () {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Cannot withdraw from a frozen account');

    $this->state->withdraw($this->account, 50);
});



it('throws exception if freeze is called again', function () {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Account is already frozen');

    $this->state->freeze($this->account);
});

it('suspends account successfully', function () {
    $account = $this->state->suspend($this->account);

    expect($account->status)->toEqual(AccountStatus::SUSPENDED);
});
