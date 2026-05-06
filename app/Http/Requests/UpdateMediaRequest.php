<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class UpdateMediaRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'external_id' => 'nullable|integer',
            'source' => 'nullable|string',
            'type' => 'sometimes|in:anime,movie,series',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',

            'images' => 'nullable|array',
            'status' => 'nullable|string',

            'airing' => 'boolean',
            'episodes' => 'nullable|integer',
            'duration' => 'nullable|string',
            'year' => 'nullable|integer',
'cover' => 'nullable|string',
            'score' => 'nullable|numeric',
            'scored_by' => 'nullable|integer',

            'rank' => 'nullable|integer',
            'popularity' => 'nullable|integer',
            'members' => 'nullable|integer',
            'favorites' => 'nullable|integer',

            'approved' => 'boolean',
            'is_public' => 'boolean',

            'studios' => 'nullable|array',
            'genres' => 'nullable|array',
        ];
    }
}

