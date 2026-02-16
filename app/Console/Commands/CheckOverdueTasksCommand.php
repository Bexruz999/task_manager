<?php

namespace App\Console\Commands;

use App\Enums\TaskStatus;
use App\Jobs\HandleOverdueTasksJob;
use App\Models\Task;
use App\Notifications\TaskReminderNotification;
use Illuminate\Console\Command;

class CheckOverdueTasksCommand extends Command
{
    protected $signature = 'tasks:overdue-tasks';

    protected $description = 'Command description';

    public function handle(): void
    {
        $tasksToRemind = Task::whereNull('reminder_sent_at')
            ->where('status',TaskStatus::Pending)
            ->whereRaw('DATE_SUB(deadline, INTERVAL remind_before_minutes MINUTE) <= ?', [now()])
            ->get();

        foreach ($tasksToRemind as $task) {
            $task->user->notify(new TaskReminderNotification($task));

            $task->update(['reminder_sent_at' => now()]);

            $this->info("Примечание добавлено в очередь: Task #$task->id");
        }

        $overdueTasks = Task::where('status', 'pending')
            ->where('deadline', '<', now())
            ->get();

        foreach ($overdueTasks as $task) {
            HandleOverdueTasksJob::dispatch($task);
        }
    }
}
