<?php

namespace App\Http\Requests\Lesson;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'lesson_id'   => 'required|integer|exists:lessons,id',
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'order'       => 'sometimes|required|integer|min:1',
            'files.*'     => 'nullable|file|max:20480',
        ];
    }
}
