<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Log;

class TaskReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via(): array
    {
        return $this->task->remind_via === 'email' ? ['mail'] : ['sms'];
    }

    /**
     * Создание сообщения электронной почты.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Напоминание о задаче: ' . $this->task->title)
            ->greeting('Привет, ' . $notifiable->name)
            ->line('У вас есть предстоящая задача:')
            ->line('**Задача:** ' . $this->task->title)
            ->line('**Срок:** ' . $this->task->deadline->format('d.m.Y H:i'))
            ->line('Не забудь сделать это вовремя!');
    }

    public function toSms($notifiable): void
    {
        Log::channel('single')->info("SMS MOCK: Уведомление для $notifiable->name. Задача: {$this->task->title}. Дедлайн: {$this->task->deadline}");
    }
}
