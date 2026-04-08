<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255|min:3',
            'description' => 'sometimes|string|min:10|max:5000',
            'status' => 'sometimes|in:draft,published,archived',
            'is_featured' => 'sometimes|boolean',
        ];
    }
}
