<?php

namespace App\Modules\Accounts\Facades;

use Illuminate\Support\Facades\Facade;

class AccountFacade extends Facade
{
    //هي الدالة هي نقطة الربط بين الـ Facade والـ Service Container
    protected static function getFacadeAccessor()
    {
        return 'account.manager'; // المفتاح الذي سنربطه في الـ container
    }
}
