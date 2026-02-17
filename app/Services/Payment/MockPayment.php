<?php

namespace App\Services\Payment;

use App\Contracts\PaymentInterface;
use App\Contracts\SmsNotificationInterface;
use App\Models\Order;

class MockPayment implements PaymentInterface
{
    public function createPayment(Order $order): array
    {
        return [
            'payment_url' => "https://checkout.mock-payment.com/" . uniqid(),
            'transaction_id' => "txn_" . bin2hex(random_bytes(4))
        ];
    }

    public function verifyWebhook(array $payload, string $signature): bool
    {
        $secret = config('services.payment.secret');
        // HMAC-SHA256 tekshiruvi
        $computedSignature = hash_hmac('sha256', json_encode($payload), $secret);

        return hash_equals($computedSignature, $signature);
    }
}
