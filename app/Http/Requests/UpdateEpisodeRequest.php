<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEpisodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'number' => 'sometimes|integer|min:1',
            'title' => 'nullable|string|max:255',
            'duration' => 'nullable|integer',
            'video_url' => 'nullable|string',
        ];
    }
}