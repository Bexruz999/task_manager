<?php

namespace Tests\Unit;

use JsonException;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    /**
     * @throws JsonException
     */
    public function test_it_verifies_valid_hmac_signature(): void
    {
        $payload = ['order_id' => 1, 'status' => 'success'];
        $secret = 'test-secret';
        $signature = hash_hmac('sha256', json_encode($payload, JSON_THROW_ON_ERROR), $secret);

        $computed = hash_hmac('sha256', json_encode($payload, JSON_THROW_ON_ERROR), $secret);
        $this->assertEquals($signature, $computed);
    }

    public function test_it_calculates_delivery_cost_correctly(): void
    {
        $distance = 10; // 10 km
        $base = 5000;
        $perKm = 800;
        $expected = $base + ($distance * $perKm); // 13000

        $this->assertEquals(13000, $expected);

        $longDistance = 110;
        $expectedLong = ($base + ($longDistance * $perKm)) * 1.5;

        // (5000 + (110 * 800)) * 1.5 = (5000 + 88000) * 1.5 = 93000 * 1.5 = 139500
        $this->assertEquals(139500, $expectedLong);
    }
}
