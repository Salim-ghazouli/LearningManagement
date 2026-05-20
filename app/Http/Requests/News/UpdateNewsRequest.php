<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "new_id" => "required|integer|exists:news,id",
            'title'   => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'order'   => 'sometimes|required|integer|min:1',
        ];
    }
}
