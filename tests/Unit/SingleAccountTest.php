<?php

use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Accounts\Composites\SingleAccount;

beforeEach(function () {
    // إعداد لكل Test
    $this->bankAccount = new BankAccount();
    $this->bankAccount->balance = 1000;

    $this->singleAccount = new SingleAccount($this->bankAccount);
});

it('gets the balance', function () {
    expect($this->singleAccount->getBalance())->toEqual(1000);
});

it('throws exception when adding child', function () {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Cannot add child to a single account');

    $this->singleAccount->addChild($this->singleAccount);
});

it('throws exception when removing child', function () {
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Cannot remove child from a single account');

    $this->singleAccount->removeChild($this->singleAccount);
});

it('returns empty array for getChildren', function () {
    expect($this->singleAccount->getChildren())->toBeArray()->toHaveCount(0);
});

it('returns the bank account model', function () {
    expect($this->singleAccount->getModel())->toBe($this->bankAccount);
});
