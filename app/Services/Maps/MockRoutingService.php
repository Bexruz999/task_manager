<?php

namespace App\Services\Maps;

use App\Contracts\RoutingInterface;

class MockRoutingService implements RoutingInterface
{
    public function calculateRoute(array $from, array $to): array
    {
        $earthRadius = 6371;
        $dLat = deg2rad($to['lat'] - $from['lat']);
        $dLng = deg2rad($to['lng'] - $from['lng']);

        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($from['lat'])) * cos(deg2rad($to['lat'])) *
            sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        $finalDistance = $distance * 1.3;
        $duration = ($finalDistance / 40) * 60;

        return [
            'distance' => round($finalDistance, 2),
            'duration' => round($duration),
        ];
    }
}
