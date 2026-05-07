<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class StoreSeasonRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'number' => ['required', 'integer', 'min:1', 'max:999'],
        ];
    }
}