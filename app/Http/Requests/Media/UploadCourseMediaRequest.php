<?php

namespace App\Http\Requests\Media;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadCourseMediaRequest extends FormRequest
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
            'course_id' => 'required|exists:courses,id',
            'course_images' => 'nullable|array',
            'course_images.*'  => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'course_files' => 'nullable|array',
            'course_files.*' => 'nullable|file|mimes:pdf|max:10240',
        ];
    }
}
