<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'media_id' => ['required', 'exists:media,id'],
            'status' => ['nullable', 'in:planned,watching,completed,dropped'],
        ];
    }
}