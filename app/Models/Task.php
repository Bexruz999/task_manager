<?php

namespace App\Models;

use App\Enums\RecurrenceType;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Observers\TaskObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy([TaskObserver::class])]
class Task extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'type',
        'title',
        'description',
        'priority',
        'status',
        'deadline',
        'is_recurring',
        'recurrence_type',
        'remind_before_minutes',
        'remind_via',
        'reminder_sent_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => TaskStatus::class,
        'priority' => TaskPriority::class,
        'type' => TaskType::class,
        'recurrence_type' => RecurrenceType::class,
        'deadline' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }
}
