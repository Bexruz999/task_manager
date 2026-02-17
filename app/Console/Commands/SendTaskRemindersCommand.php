<?php

namespace App\Console\Commands;

use App\Enums\TaskStatus;
use App\Jobs\HandleOverdueTasksJob;
use App\Jobs\SendReminderJob;
use App\Models\Task;
use Illuminate\Console\Command;

class SendTaskRemindersCommand extends Command
{
    protected $signature = 'tasks:send-reminders';
    protected $description = 'Отправка напоминаний и проверка просроченных';

    public function handle(): void
    {
        $this->handleReminders();

        $this->handleOverdueTasks();
    }

    private function handleReminders(): void
    {
        Task::where('status', TaskStatus::Pending)
            ->whereNotNull('remind_before_minutes')
            ->whereNull('reminder_sent_at')
            ->chunkById(100, function ($tasks) {
                foreach ($tasks as $task) {

                    $deadline = $task->deadline;
                    $remindBefore = $task->remind_before_minutes;
                    $reminderTime = $deadline->copy()->subMinutes($remindBefore);

                    if ($reminderTime->isPast()) {
                        SendReminderJob::dispatch($task);

                        $task->update(['reminder_sent_at' => now()]);
                    }
                }
            });
    }

    private function handleOverdueTasks(): void
    {

        Task::where('status', TaskStatus::Pending)
            ->where('deadline', '<', now())
            ->where(function ($query) {
                $query->whereNull('reminder_sent_at')
                    ->orWhereDate('reminder_sent_at', '<', now()->toDateString());
            })
            ->chunkById(100, function ($tasks) {
                foreach ($tasks as $task) {
                    $this->error("Задача #$task->id просрочена! Крайний срок: $task->deadline");

                    HandleOverdueTasksJob::dispatch($task);

                    $task->update(['reminder_sent_at' => now()]);
                }
            });
    }
}
