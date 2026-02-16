<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Notifications\TaskStatusChangedNotification;
use Carbon\Carbon;
use Exception;
use Log;
use RuntimeException;

class TaskService
{

    /**
     * Создание Задачи
     */
    public function createTask(array $data, $user)
    {
        return $user->tasks()->with('client')->create($data);
    }

    /**
     * Обновления Задачи
     */
    public function updateTask(Task $task, array $data): ?Task
    {
        if (isset($data['status']) && $data['status'] !== $task->status->value) {
            $this->updateStatus($task, $data['status']);
            unset($data['status']);
        }

        $task->update($data);

        return $task->fresh();
    }

    /**
     * Обработка статуса и создание повторной задачи
     */
    public function updateStatus(Task $task, string $statusValue): Task
    {
        $newStatus = TaskStatus::tryFrom($statusValue);
        $currentStatus = $task->status;

        if ($currentStatus === $newStatus) {
            return $task;
        }

        // Валидация перехода (согласно вашей таблице переходов)
        $this->validateTransition($currentStatus, $newStatus);

        $task->status = $newStatus;

        $task->user->notify(new TaskStatusChangedNotification($task));

        if ($newStatus === TaskStatus::Done) {
            $task->completed_at = now();

            // Если задача повторяющаяся, создаем копию
            if ($task->is_recurring) {
                try {

                    $this->createRecurringCopy($task);
                } catch (Exception $exception) {
                    Log::error($exception->getMessage());
                }
            }
        }

        $task->save();
        return $task;
    }

    /**
     * Создание копии задачи (Логика повторяющихся задач)
     */
    protected function createRecurringCopy(Task $task): ?Task
    {
        // 1. Расчет нового дедлайн: текущий + период
        $nextDeadline = match ($task->recurrence_type->value) {
            'daily'   => Carbon::parse($task->deadline)->addDay(),
            'weekly'  => Carbon::parse($task->deadline)->addWeek(),
            'monthly' => Carbon::parse($task->deadline)->addMonth(),
            default   => null,
        };

        if ($nextDeadline) {
            // 2. Наследование данных: тип, описание, приоритет, клиент, исполнитель, настройки напоминания
            $newTask = $task->replicate([
                'status',
                'completed_at',
                'reminder_sent_at',
                'deadline'
            ]);

            // 3. Установка новых значений
            $newTask->status = TaskStatus::Pending;
            $newTask->deadline = $nextDeadline;

            // Сохраняем новую задачу
            $newTask->save();

            return $newTask;
        }

        return null;
    }

    /**
     * Правила переходов статусов
     */
    protected function validateTransition($current, $new): void
    {
        $allowed = [
            TaskStatus::Pending->value => [TaskStatus::InProgress->value, TaskStatus::Cancelled->value],
            TaskStatus::InProgress->value => [TaskStatus::Done->value, TaskStatus::Cancelled->value],
            TaskStatus::Done->value => [],
            TaskStatus::Cancelled->value => [],
        ];

        if (!in_array($new->value, $allowed[$current->value] ?? [], true)) {
            throw new RuntimeException("Статус не может быть изменен с $current->value на $new->value.", 422);
        }
    }
}
