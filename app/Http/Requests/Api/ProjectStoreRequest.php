<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('projects', 'slug')->where('user_id', $this->user()->id),
            ],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,archived'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}
