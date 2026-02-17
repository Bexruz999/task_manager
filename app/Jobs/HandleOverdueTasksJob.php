<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class HandleOverdueTasksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Task $task){}

    public function handle(): void
    {
        if ($this->task->deadline->isPast()) {
            Log::warning("ПРОСРОЧЕННАЯ ЗАДАЧА: ID {$this->task->id}, Заголовок: {$this->task->title}, Исполнитель: {$this->task->user->name}");
        }
    }
}
