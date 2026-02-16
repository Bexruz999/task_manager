<?php

namespace App\Services\Maps;

use App\Contracts\GeocoderInterface;

class MockGeocoderService implements GeocoderInterface
{
    public function geocode(string $address): array
    {
        return [
            'lat' => random_int(41280000, 41350000) / 1000000,
            'lng' => random_int(69200000, 69350000) / 1000000,
        ];
    }
}
