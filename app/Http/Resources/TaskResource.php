<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'project_id' => $this->project_id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status->value,
            'priority' => $this->priority->value,
            'due_date' => optional($this->due_date)?->toDateString(),
            'completed_at' => optional($this->completed_at)?->toDateTimeString(),
            'is_reminder_sent' => $this->is_reminder_sent,
            'is_due_date_holiday' => (bool) ($this->is_due_date_holiday ?? false),
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
                'color' => $this->category->color,
            ] : null,
            'project' => $this->project ? [
                'id' => $this->project->id,
                'name' => $this->project->name,
                'status' => $this->project->status,
            ] : null,
            'comments_count' => $this->whenCounted('comments'),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
