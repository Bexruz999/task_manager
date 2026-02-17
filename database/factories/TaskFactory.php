<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\Client;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Task> */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['call', 'meeting', 'email', 'task']),
            'title' => fake()->randomElement([
                'Перезвонить клиенту ' . fake()->lastName(),
                'Встреча по проекту ' . fake()->company(),
                'Подготовить договор для ' . fake()->name(),
                'Отправить отчет за неделю'
            ]),
            'description' => fake()->sentence(),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'status' => TaskStatus::Pending,
            'deadline' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'remind_before_minutes' => fake()->randomElement([15, 30, 60]),
            'remind_via' => 'email',
            'is_recurring' => true,
            'recurrence_type' => function (array $attributes) {
                return $attributes['is_recurring'] ? fake()->randomElement(['daily', 'weekly', 'monthly']) : null;
            },
            'client_id' => Client::factory(),
            'user_id' => User::factory(),
        ];
    }
}
