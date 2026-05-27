<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaskStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:categories,id'],
            'project_id' => [
                'nullable',
                Rule::exists('projects', 'id')->where('user_id', $this->user()->id),
            ],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', 'in:low,medium,high'],
            'status' => ['nullable', 'in:new,in_progress,done,archived'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}
