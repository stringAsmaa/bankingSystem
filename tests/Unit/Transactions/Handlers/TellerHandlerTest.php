<?php

use App\Modules\Transactions\Handlers\TellerHandler;
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
| TellerHandler Unit Tests
|--------------------------------------------------------------------------
*/

it('approves transaction when amount is within teller range and user is teller', function () {
    $tellerUser = new class {
        public int $id = 3;
        public function hasRole(string $role): bool
        {
            return $role === 'Teller';
        }
    };

    Auth::shouldReceive('user')->once()->andReturn($tellerUser);

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
    $transaction->transaction_amount = 7000; // بين 5000 و 10000
    $transaction->transaction_currency = 'USD';

    $transaction = Mockery::mock($transaction)->makePartial();
    $transaction->shouldReceive('approveBy')
        ->with(3)
        ->once()
        ->andReturn($transaction);

    $handler = new TellerHandler();

    $result = $handler->handle($transaction);

    expect($result)->toBe($transaction);
});

it('throws exception when amount is in teller range but user is not teller', function () {
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
    $transaction->transaction_amount = 6000;
    $transaction->transaction_currency = 'USD';

    $handler = new TellerHandler();

    expect(fn () => $handler->handle($transaction))
        ->toThrow(Exception::class, 'Only Teller can approve this transaction');
});

it('passes transaction to next handler when amount is greater than teller range', function () {
    $tellerUser = new class {
        public function hasRole(string $role): bool
        {
            return true;
        }
    };

    Auth::shouldReceive('user')->once()->andReturn($tellerUser);

    $setting = (object) ['max_amount' => 5000];

    Mockery::mock('alias:' . TransactionSetting::class)
        ->shouldReceive('where')
        ->once()
        ->andReturnSelf()
        ->shouldReceive('first')
        ->once()
        ->andReturn($setting);

    $transaction = new Transaction();
    $transaction->transaction_amount = 12000; // > 10000
    $transaction->transaction_currency = 'USD';

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

it('throws exception when amount requires higher approval and no next handler exists', function () {
    $tellerUser = new class {
        public function hasRole(string $role): bool
        {
            return true;
        }
    };

    Auth::shouldReceive('user')->once()->andReturn($tellerUser);

    $setting = (object) ['max_amount' => 5000];

    Mockery::mock('alias:' . TransactionSetting::class)
        ->shouldReceive('where')
        ->once()
        ->andReturnSelf()
        ->shouldReceive('first')
        ->once()
        ->andReturn($setting);

    $transaction = new Transaction();
    $transaction->transaction_amount = 12000;
    $transaction->transaction_currency = 'USD';

    $handler = new TellerHandler();

    expect(fn () => $handler->handle($transaction))
        ->toThrow(Exception::class, 'Transaction requires higher approval');
});
