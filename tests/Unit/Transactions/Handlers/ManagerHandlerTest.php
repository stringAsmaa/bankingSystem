<?php

use App\Modules\Transactions\Handlers\ManagerHandler;
use App\Modules\Transactions\Handlers\TransactionHandler;
use App\Modules\Transactions\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Mockery;

uses(TestCase::class);

afterEach(function () {
    Mockery::close();
});

it('approves transaction when amount is <= 10000 and user is manager', function () {
    $managerUser = new class {
        public int $id = 2;
        public function hasRole(string $role): bool
        {
            return $role === 'Manager';
        }
    };

    Auth::shouldReceive('user')->once()->andReturn($managerUser);

    $transaction = new Transaction();
    $transaction->transaction_amount = 8000;

    $transaction = Mockery::mock($transaction)->makePartial();
    $transaction->shouldReceive('approveBy')
        ->with(2)
        ->once()
        ->andReturn($transaction);

    $handler = new ManagerHandler();

    $result = $handler->handle($transaction);

    expect($result)->toBe($transaction);
});

it('throws exception when amount is <= 10000 and user is not manager', function () {
    $user = new class {
        public function hasRole(string $role): bool
        {
            return false;
        }
    };

    Auth::shouldReceive('user')->once()->andReturn($user);

    $transaction = new Transaction();
    $transaction->transaction_amount = 5000;

    $handler = new ManagerHandler();

    expect(fn () => $handler->handle($transaction))
        ->toThrow(Exception::class, 'Only Manager can approve this transaction');
});

it('passes transaction to next handler when amount is greater than 10000', function () {
    $managerUser = new class {
        public function hasRole(string $role): bool
        {
            return true;
        }
    };

    Auth::shouldReceive('user')->once()->andReturn($managerUser);

    $transaction = new Transaction();
    $transaction->transaction_amount = 15000;

    $nextHandler = Mockery::mock(TransactionHandler::class);
    $nextHandler->shouldReceive('handle')
        ->with($transaction)
        ->once()
        ->andReturn($transaction);

    $handler = new ManagerHandler();
    $handler->setNext($nextHandler);

    $result = $handler->handle($transaction);

    expect($result)->toBe($transaction);
});

it('throws exception when amount is greater than 10000 and no next handler exists', function () {
    $managerUser = new class {
        public function hasRole(string $role): bool
        {
            return true;
        }
    };

    Auth::shouldReceive('user')->once()->andReturn($managerUser);

    $transaction = new Transaction();
    $transaction->transaction_amount = 20000;

    $handler = new ManagerHandler();

    expect(fn () => $handler->handle($transaction))
        ->toThrow(Exception::class, 'Transaction requires Admin approval');
});
