<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:planned,watching,completed,dropped'],
            'progress' => ['nullable', 'integer', 'min:0'],
            'started_at' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
        ];
    }
}