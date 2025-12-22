<?php

use App\Modules\Transactions\Handlers\AdminHandler;
use App\Modules\Transactions\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use Mockery;

uses(TestCase::class);

afterEach(function () {
    Mockery::close();
});

it('approves transaction when user is admin', function () {
    // Fake user (NOT Eloquent)
    $adminUser = new class {
        public int $id = 1;

        public function hasRole(string $role): bool
        {
            return $role === 'Admin';
        }
    };

    // Mock auth()->user()
    Auth::shouldReceive('user')
        ->once()
        ->andReturn($adminUser);

    $transaction = Mockery::mock(Transaction::class);
    $transaction->shouldReceive('approveBy')
        ->with(1)
        ->once()
        ->andReturn($transaction);

    $handler = new AdminHandler();

    $result = $handler->handle($transaction);

    expect($result)->toBe($transaction);
});

it('throws exception when user is not admin', function () {
    $user = new class {
        public function hasRole(string $role): bool
        {
            return false;
        }
    };

    Auth::shouldReceive('user')
        ->once()
        ->andReturn($user);

    $transaction = Mockery::mock(Transaction::class);

    $handler = new AdminHandler();

    expect(fn () => $handler->handle($transaction))
        ->toThrow(Exception::class, 'Only Admin can approve this transaction');
});
