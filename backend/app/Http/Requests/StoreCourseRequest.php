<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (!$user) return false;
        return $user->can('manage courses') || $user->hasRole(['admin', 'super-admin', 'teacher', 'content-manager']);
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|min:3',
            'description' => 'required|string|min:10|max:5000',
            'status' => 'sometimes|in:draft,published,archived',
            'is_featured' => 'sometimes|boolean',
        ];
    }
}
