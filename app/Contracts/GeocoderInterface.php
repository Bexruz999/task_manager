<?php

namespace App\Contracts;

interface GeocoderInterface
{
    public function geocode(string $address): array;
}
