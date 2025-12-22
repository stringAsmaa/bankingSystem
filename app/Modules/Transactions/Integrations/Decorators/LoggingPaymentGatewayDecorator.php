<?php

namespace App\Modules\Transactions\Integrations\Decorators;

use App\Modules\Transactions\Integrations\PaymentGateway;

class LoggingPaymentGatewayDecorator implements PaymentGateway
{
    protected PaymentGateway $gateway;

    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    public function createCheckout(array $payload): array
    {
        // قبل التنفيذ: تسجيل البيانات الواردة
        logger()->info('Payment checkout started', [
            'payload' => $payload,
        ]);

        // تنفيذ العملية الأصلية
        $response = $this->gateway->createCheckout($payload);

        // بعد التنفيذ: تسجيل النتيجة
        logger()->info('Payment checkout finished', [
            'response' => $response,
        ]);

        return $response;
    }
}
