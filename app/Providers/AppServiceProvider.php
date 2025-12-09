<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
