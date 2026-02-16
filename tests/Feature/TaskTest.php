<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_create_task_with_reminder(): void
    {
        $taskData = [
            'title'    => 'Важная встреча',
            'type'     => 'meeting',
            'priority' => 'high',
            'deadline' => Carbon::now()->addDays()->toDateTimeString(),
            'remind_before_minutes' => 30,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/tasks', $taskData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', ['title' => 'Важная встреча']);
    }

    public function test_can_get_today_and_overdue_tasks(): void
    {
        Task::factory()->create([
            'user_id' => $this->user->id,
            'deadline' => Carbon::now()->setHour(10)
        ]);

        Task::factory()->create([
            'user_id' => $this->user->id,
            'deadline' => Carbon::now()->subDay()
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/tasks?filter=today_and_overdue');

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(2, count($response->json('data')));
    }

    public function test_recurring_task_creates_new_one_on_done(): void
    {
        $task = Task::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'in_progress',
            'is_recurring' => true,
            'recurrence_type' => 'daily',
            'type' => 'meeting',
            'priority' => 'high',
            'deadline' => now()->addDay(),
        ]);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/tasks/$task->id/status", [
                'status' => 'done'
            ]);

        if ($response->status() !== 200) {
            dump($response->json());
        }

        $response->assertStatus(200);

        $this->assertEquals(2, Task::count());

        $this->assertDatabaseHas('tasks', [
            'status' => 'pending',
            'title' => $task->title
        ]);
    }

    public function test_user_cannot_update_others_task(): void
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id, 'status' => 'in_progress']);

        $response = $this->actingAs($this->user)
            ->patchJson("/api/tasks/$task->id/status", ['status' => 'done']);
        $response->assertStatus(403);
    }

    public function test_deadline_cannot_be_in_the_past(): void
    {
        $taskData = [
            'title' => 'Старая задача',
            'type' => 'meeting',
            'priority' => 'low',
            'deadline' => now()->subDay()->toDateTimeString(),
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/tasks', $taskData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['deadline']);
    }

    public function test_can_delete_task(): void
    {
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/tasks/$task->id");

        $response->assertStatus(204);
        $this->assertSoftDeleted('tasks', ['id' => $task->id]);
    }
}
