<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255|min:3',
            'description' => 'nullable|string|max:2000',
            'duration' => 'sometimes|integer|min:5|max:180',
            'difficulty' => 'sometimes|in:easy,medium,hard',
            'status' => 'sometimes|in:draft,published,archived',
        ];
    }
}
