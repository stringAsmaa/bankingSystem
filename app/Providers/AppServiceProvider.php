<?php

namespace App\Providers;

use App\Modules\Accounts\Models\BankAccount;
use App\Modules\Accounts\Observers\BankAccountObserver;
use App\Modules\Transactions\Integrations\PaymentGateway;
use App\Modules\Transactions\Integrations\StripeAdapter;
use App\Modules\Transactions\Models\Transaction;
use App\Modules\Transactions\Observers\TransactionObserver;
use App\Modules\Transactions\Repositories\TransactionRepository;
use App\Modules\Transactions\Repositories\TransactionRepositoryInterface;
use App\Modules\Transactions\Services\RecommendationService;
use App\Modules\Transactions\Strategies\HighSpendingStrategy;
use App\Modules\Transactions\Strategies\RecurringTransactionsStrategy;
use App\Modules\Transactions\Strategies\StableActivityStrategy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            TransactionRepositoryInterface::class,
            TransactionRepository::class
        );
        $this->app->bind(
            PaymentGateway::class,
            StripeAdapter::class
        );
        $this->app->bind(
            \App\Modules\accounts\Repositories\BankAccountRepositoryInterface::class,
            \App\Modules\accounts\Repositories\BankAccountRepository::class
        );

        /*
    عمل ال make هون
    إنشاء UserService
تطبيق Dependency Injection
حل أي Dependencies إضافية يحتاجها UserService تلقائيًا
ونفس الشيء مع ClientService وBankAccountService.
*/
        $this->app->singleton('account.manager', function ($app) {
            return new \App\Modules\Accounts\Services\AccountManager(
                $app->make(\App\Modules\Accounts\Services\UserService::class),
                $app->make(\App\Modules\Accounts\Services\ClientService::class),
                $app->make(\App\Modules\Accounts\Services\BankAccountService::class),
            );
        });

        $this->app->singleton(RecommendationService::class, function () {
            return new RecommendationService([
                new HighSpendingStrategy(),
                new RecurringTransactionsStrategy(),
                new StableActivityStrategy(),
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        BankAccount::observe(BankAccountObserver::class);
        Transaction::observe(TransactionObserver::class);
    }
}
