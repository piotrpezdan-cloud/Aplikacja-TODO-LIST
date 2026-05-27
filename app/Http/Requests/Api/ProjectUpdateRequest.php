<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $projectId = $this->route('project')?->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'nullable',
                'string',
                'max:255',
                Rule::unique('projects', 'slug')
                    ->where('user_id', $this->user()->id)
                    ->ignore($projectId),
            ],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'in:active,archived'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}
