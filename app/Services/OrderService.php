<?php

namespace App\Services;

class OrderService
{
    public function calculateCost(float $distance): float
    {
        $baseRate = 5000;
        $perKm = 800;

        $cost = $baseRate + ($distance * $perKm);

        if ($distance > 100) {
            $cost *= 1.5;
        }

        return round($cost, 2);
    }
}
