<?php

use App\Modules\accounts\Services\BankAccountStateService;
use App\Modules\accounts\Repositories\BankAccountRepositoryInterface;
use App\Modules\accounts\Models\BankAccount;
use App\Modules\accounts\Composites\AccountComponent;
use Mockery;

beforeEach(function () {
    $this->repo = Mockery::mock(BankAccountRepositoryInterface::class);
});

it('throws exception if account not found', function () {
    $this->repo
        ->shouldReceive('find')
        ->with(1)
        ->andReturn(null);

    $service = new BankAccountStateService($this->repo);

    expect(fn () => $service->deposit(1, 100))
        ->toThrow(Exception::class, 'Account not found');
});

it('calls deposit on account component', function () {
    $account = Mockery::mock(BankAccount::class);

    $component = Mockery::mock(AccountComponent::class);
    $component->shouldReceive('deposit')->with(100)->once();
    $component->shouldReceive('getModel')->andReturn($account);

    $service = Mockery::mock(
        BankAccountStateService::class,
        [$this->repo]
    )
    ->makePartial()
    ->shouldAllowMockingProtectedMethods();

    $service
        ->shouldReceive('getAccountComponent')
        ->with(1)
        ->andReturn($component);

    $result = $service->deposit(1, 100);

    expect($result)->toBe($account);
});

it('calls withdraw on account component', function () {
    $account = Mockery::mock(BankAccount::class);

    $component = Mockery::mock(AccountComponent::class);
    $component->shouldReceive('withdraw')->with(50)->once();
    $component->shouldReceive('getModel')->andReturn($account);

    $service = Mockery::mock(
        BankAccountStateService::class,
        [$this->repo]
    )
    ->makePartial()
    ->shouldAllowMockingProtectedMethods(); // ← هذا السطر مهم

    $service
        ->shouldReceive('getAccountComponent')
        ->with(2)
        ->andReturn($component);

    $result = $service->withdraw(2, 50);

    expect($result)->toBe($account);
});

it('calls close on account component', function () {
    $account = Mockery::mock(BankAccount::class);

    $component = Mockery::mock(AccountComponent::class);
    $component->shouldReceive('close')->once();
    $component->shouldReceive('getModel')->andReturn($account);

    $service = Mockery::mock(
        BankAccountStateService::class,
        [$this->repo]
    )
    ->makePartial()
    ->shouldAllowMockingProtectedMethods(); // ← نفس الشيء

    $service
        ->shouldReceive('getAccountComponent')
        ->with(3)
        ->andReturn($component);

    $result = $service->close(3);

    expect($result)->toBe($account);
});
