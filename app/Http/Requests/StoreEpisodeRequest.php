<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEpisodeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'number' => 'required|integer|min:1',
            'title' => 'nuAllable|string|max:255',
            'duration' => 'nullable|integer',
            'video_url' => 'nullable|string',
        ];
    }
}