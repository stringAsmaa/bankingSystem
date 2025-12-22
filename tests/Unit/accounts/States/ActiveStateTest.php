<?php

use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Accounts\States\ActiveState;

beforeEach(function () {
    $this->state = new ActiveState();

    $this->account = Mockery::mock(BankAccount::class)->makePartial();
    $this->account->balance = 100;

    // Mock لدوال save و setState
    $this->account->shouldReceive('save')->andReturnTrue();
    $this->account->shouldReceive('setState')->andReturnSelf();
});

it('increases balance when deposit is called', function () {
    $amount = 50;
    $account = $this->state->deposit($this->account, $amount);

    expect($account->balance)->toEqual(150);
});

it('decreases balance when withdraw is called', function () {
    $amount = 40;
    $account = $this->state->withdraw($this->account, $amount);

    expect($account->balance)->toEqual(60);
});

it('throws exception if withdraw amount exceeds balance', function () {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Insufficient balance');

    $this->state->withdraw($this->account, 200);
});
