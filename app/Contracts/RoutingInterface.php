<?php

namespace App\Contracts;

interface RoutingInterface {
    public function calculateRoute(array $from, array $to): array; // [distance, duration]
}
