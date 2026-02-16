<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Task $task){}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Статус задачи изменился: " . $this->task->title)
            ->greeting("Привет, " . $notifiable->name)
            ->line("Статус вашей задачи обновлен.")
            ->line("**Задача:** " . $this->task->title)
            ->line("**Новое состояние:** " . $this->task->status->value);
    }

    public function toArray(): array
    {
        return [];
    }
}
