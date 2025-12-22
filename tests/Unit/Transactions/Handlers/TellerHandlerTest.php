<?php

use App\Modules\Transactions\Handlers\TellerHandler;
use App\Modules\Transactions\Handlers\TransactionHandler;
use App\Modules\Transactions\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Mockery;

uses(TestCase::class);

afterEach(function () {
    Mockery::close();
});

/*
|--------------------------------------------------------------------------
| TellerHandler Unit Tests
|--------------------------------------------------------------------------
*/

it('approves transaction when amount is <= 5000 and user is teller', function () {
    $tellerUser = new class {
        public int $id = 3;

        public function hasRole(string $role): bool
        {
            return $role === 'Teller';
        }
    };

    Auth::shouldReceive('user')->once()->andReturn($tellerUser);

    $transaction = new Transaction();
    $transaction->transaction_amount = 4000;

    $transaction = Mockery::mock($transaction)->makePartial();
    $transaction->shouldReceive('approveBy')
        ->with(3)
        ->once()
        ->andReturn($transaction);

    $handler = new TellerHandler();

    $result = $handler->handle($transaction);

    expect($result)->toBe($transaction);
});

it('throws exception when amount is <= 5000 and user is not teller', function () {
    $user = new class {
        public function hasRole(string $role): bool
        {
            return false;
        }
    };

    Auth::shouldReceive('user')->once()->andReturn($user);

    $transaction = new Transaction();
    $transaction->transaction_amount = 3000;

    $handler = new TellerHandler();

    expect(fn () => $handler->handle($transaction))
        ->toThrow(Exception::class, 'Only Teller can approve this transaction');
});

it('passes transaction to next handler when amount is greater than 5000', function () {
    $tellerUser = new class {
        public function hasRole(string $role): bool
        {
            return true;
        }
    };

    Auth::shouldReceive('user')->once()->andReturn($tellerUser);

    $transaction = new Transaction();
    $transaction->transaction_amount = 7000;

    $nextHandler = Mockery::mock(TransactionHandler::class);
    $nextHandler->shouldReceive('handle')
        ->with($transaction)
        ->once()
        ->andReturn($transaction);

    $handler = new TellerHandler();
    $handler->setNext($nextHandler);

    $result = $handler->handle($transaction);

    expect($result)->toBe($transaction);
});

it('throws exception when amount is greater than 5000 and no next handler exists', function () {
    $tellerUser = new class {
        public function hasRole(string $role): bool
        {
            return true;
        }
    };

    Auth::shouldReceive('user')->once()->andReturn($tellerUser);

    $transaction = new Transaction();
    $transaction->transaction_amount = 8000;

    $handler = new TellerHandler();

    expect(fn () => $handler->handle($transaction))
        ->toThrow(Exception::class, 'Transaction requires higher approval');
});
