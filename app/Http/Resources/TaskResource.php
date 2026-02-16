<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Task */
class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'deadline' => $this->deadline->format('Y-m-d\TH:i:sp'),
            'is_recurring' => $this->is_recurring,
            'recurrence_type' => $this->recurrence_type,
            'client' => $this->client ? [
                'id' => $this->client->id,
                'name' => $this->client->name,
            ] : null,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
        ];
    }
}
