<?php

use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Accounts\States\ClosedState;

beforeEach(function () {
    $this->state = new ClosedState();

    // Mock لكائن BankAccount
    $this->account = Mockery::mock(BankAccount::class)->makePartial();
    $this->account->balance = 100;
});

it('throws exception on deposit', function () {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Cannot deposit to a closed account');

    $this->state->deposit($this->account, 50);
});

it('throws exception on withdraw', function () {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Cannot withdraw from a closed account');

    $this->state->withdraw($this->account, 50);
});

it('throws exception on close', function () {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Account is already closed');

    $this->state->close($this->account);
});

it('throws exception on freeze', function () {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Cannot freeze a closed account');

    $this->state->freeze($this->account);
});

it('throws exception on suspend', function () {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Cannot suspend a closed account');

    $this->state->suspend($this->account);
});
