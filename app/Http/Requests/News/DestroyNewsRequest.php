<?php

namespace App\Http\Requests\News;

use Illuminate\Foundation\Http\FormRequest;

class DestroyNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'new_id'  => 'required|integer|exists:news,id',
        ];
    }
}
