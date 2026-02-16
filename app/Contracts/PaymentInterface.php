<?php

namespace App\Contracts;

use App\Models\Order;

interface PaymentInterface {
    public function createPayment(Order $order): array;
    public function verifyWebhook(array $payload, string $signature): bool;
}
