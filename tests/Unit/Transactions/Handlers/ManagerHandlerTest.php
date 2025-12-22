<?php

use App\Modules\Transactions\Handlers\ManagerHandler;
use App\Modules\Transactions\Handlers\TransactionHandler;
use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Models\TransactionSetting;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Mockery;

uses(TestCase::class);

afterEach(function () {
    Mockery::close();
});

/*
|--------------------------------------------------------------------------
| ManagerHandler Unit Tests
|--------------------------------------------------------------------------
*/

it('approves transaction when amount is between 2x and 3x max_amount and user is manager', function () {
    $managerUser = new class {
        public int $id = 2;
        public function hasRole(string $role): bool
        {
            return $role === 'Manager';
        }
    };

    Auth::shouldReceive('user')->once()->andReturn($managerUser);

    // Mock TransactionSetting
    $setting = (object) ['max_amount' => 5000];

    Mockery::mock('alias:' . TransactionSetting::class)
        ->shouldReceive('where')
        ->once()
        ->with('currency', 'USD')
        ->andReturnSelf()
        ->shouldReceive('first')
        ->once()
        ->andReturn($setting);

    $transaction = new Transaction();
    $transaction->transaction_amount = 12000; // بين 10000 و 15000
    $transaction->transaction_currency = 'USD';

    $transaction = Mockery::mock($transaction)->makePartial();
    $transaction->shouldReceive('approveBy')
        ->with(2)
        ->once()
        ->andReturn($transaction);

    $handler = new ManagerHandler();

    $result = $handler->handle($transaction);

    expect($result)->toBe($transaction);
});

it('throws exception when amount is in manager range but user is not manager', function () {
    $user = new class {
        public function hasRole(string $role): bool
        {
            return false;
        }
    };

    Auth::shouldReceive('user')->once()->andReturn($user);

    $setting = (object) ['max_amount' => 5000];

    Mockery::mock('alias:' . TransactionSetting::class)
        ->shouldReceive('where')
        ->once()
        ->andReturnSelf()
        ->shouldReceive('first')
        ->once()
        ->andReturn($setting);

    $transaction = new Transaction();
    $transaction->transaction_amount = 11000;
    $transaction->transaction_currency = 'USD';

    $handler = new ManagerHandler();

    expect(fn () => $handler->handle($transaction))
        ->toThrow(Exception::class, 'Only Manager can approve this transaction');
});

it('passes transaction to next handler when amount is greater than 3x max_amount', function () {
    $managerUser = new class {
        public function hasRole(string $role): bool
        {
            return true;
        }
    };

    Auth::shouldReceive('user')->once()->andReturn($managerUser);

    $setting = (object) ['max_amount' => 5000];

    Mockery::mock('alias:' . TransactionSetting::class)
        ->shouldReceive('where')
        ->once()
        ->andReturnSelf()
        ->shouldReceive('first')
        ->once()
        ->andReturn($setting);

    $transaction = new Transaction();
    $transaction->transaction_amount = 20000; // > 15000
    $transaction->transaction_currency = 'USD';

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

it('throws exception when amount requires admin approval and no next handler exists', function () {
    $managerUser = new class {
        public function hasRole(string $role): bool
        {
            return true;
        }
    };

    Auth::shouldReceive('user')->once()->andReturn($managerUser);

    $setting = (object) ['max_amount' => 5000];

    Mockery::mock('alias:' . TransactionSetting::class)
        ->shouldReceive('where')
        ->once()
        ->andReturnSelf()
        ->shouldReceive('first')
        ->once()
        ->andReturn($setting);

    $transaction = new Transaction();
    $transaction->transaction_amount = 20000;
    $transaction->transaction_currency = 'USD';

    $handler = new ManagerHandler();

    expect(fn () => $handler->handle($transaction))
        ->toThrow(Exception::class, 'Transaction requires Admin approval');
});
