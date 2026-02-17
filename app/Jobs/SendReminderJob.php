<?php

namespace App\Jobs;

use App\Models\Task;
use App\Notifications\TaskReminderNotification;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class SendReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Task $task){}

    public function handle(): void
    {
        try {
            $this->task->user->notify(new TaskReminderNotification($this->task));
            Log::info("Напоминание отправлено: Задача #{$this->task->id}");
        } catch (Exception $e) {
            Log::error("Идентификатор ошибки при отправке напоминания {$this->task->id}: " . $e->getMessage());
        }
    }
}
