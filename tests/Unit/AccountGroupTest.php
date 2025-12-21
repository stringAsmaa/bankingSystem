<?php

use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Accounts\Composites\AccountGroup;
use App\Modules\Accounts\Composites\SingleAccount;

beforeEach(function () {
    // الحساب الأب
    $this->parentAccount = new BankAccount();
    $this->parentAccount->balance = 1000;
    $this->parentAccount->children = [];

    $this->group = new AccountGroup($this->parentAccount);
});

it('returns correct balance without children', function () {
    expect($this->group->getBalance())->toEqual(1000);
});

it('adds a child account', function () {
    $childAccount = new BankAccount();
    $childAccount->balance = 500;

    $child = new SingleAccount($childAccount);

    $this->group->addChild($child);

    expect($this->group->getChildren())->toHaveCount(1);
});

it('calculates total balance with children', function () {
    $child1 = new SingleAccount((function () {
        $a = new BankAccount();
        $a->balance = 300;
        return $a;
    })());

    $child2 = new SingleAccount((function () {
        $a = new BankAccount();
        $a->balance = 200;
        return $a;
    })());

    $this->group->addChild($child1);
    $this->group->addChild($child2);

    // 1000 + 300 + 200
    expect($this->group->getBalance())->toEqual(1500);
});

it('removes a child account', function () {
    $child = new SingleAccount((function () {
        $a = new BankAccount();
        $a->balance = 300;
        return $a;
    })());

    $this->group->addChild($child);
    $this->group->removeChild($child);

    expect($this->group->getChildren())->toHaveCount(0);
});

it('returns the parent bank account model', function () {
    expect($this->group->getModel())->toBe($this->parentAccount);
});
