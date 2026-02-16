<?php

namespace Database\Seeders;

use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\Client;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $manager = User::factory()->create([
            'name' => 'Иванов А.А.',
            'email' => 'manager@crm.com',
            'password' => bcrypt('password'),
        ]);

        $client = Client::factory()->create([
            'name' => 'Петров И.И.',
        ]);

        Task::factory()->create([
            'user_id' => $manager->id,
            'client_id' => $client->id,
            'title' => 'Перезвонить клиенту Петрову',
            'type' => TaskType::Call,
            'priority' => 'high',
            'deadline' => now()->addDays(2),
            'is_recurring' => true,
            'recurrence_type' => 'weekly',
        ]);

        Task::factory()->count(10)->create([
            'user_id' => $manager->id,
        ]);
    }
}
