<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/** @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order> */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'customer_name' => $this->faker->name(),
            'customer_phone' => $this->faker->phoneNumber(),
            'customer_email' => $this->faker->unique()->safeEmail(),
            'origin_address' => $this->faker->address(),
            'origin_lat' => $this->faker->latitude(),
            'origin_lng' => $this->faker->longitude(),
            'destination_address' => $this->faker->address(),
            'destination_lat' => $this->faker->latitude(),
            'destination_lng' => $this->faker->longitude(),
            'distance_km' => $this->faker->randomFloat(),
            'duration_minutes' => $this->faker->randomNumber(),
            'estimated_cost' => $this->faker->randomFloat(),
            'status' => $this->faker->word(),
            'paid_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
