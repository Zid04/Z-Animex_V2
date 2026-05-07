<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'external_id' => 'nullable|integer',
            'source' => 'nullable|string',

            'type' => 'required|in:anime,movie,series',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',

            'images' => 'nullable|array',
            'status' => 'nullable|string',
             'airing'      => 'nullable|boolean',

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
            'approved' => 'nullable|boolean',
            'is_public' => 'nullable|boolean',

            'studios' => 'nullable|string',
            'genres' => 'nullable|array',
            'genres.*' => 'integer|exists:tags,id',
        ];
    }
}
