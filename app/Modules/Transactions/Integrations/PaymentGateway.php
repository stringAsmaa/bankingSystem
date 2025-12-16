<?php

namespace App\Modules\Transactions\Integrations;

interface PaymentGateway
{
    public function createCheckout(array $payload): array;
}
