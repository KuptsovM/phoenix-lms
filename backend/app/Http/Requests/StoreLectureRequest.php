<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLectureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:2000',
            'content' => 'nullable|string',
            'status' => 'sometimes|in:draft,published,archived',
            'order' => 'nullable|integer|min:0',
        ];
    }
}
