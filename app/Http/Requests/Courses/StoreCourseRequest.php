<?php

namespace App\Http\Requests\Courses;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;

class StoreCourseRequest extends FormRequest
{
    use ApiResponseTrait;
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'Instructor_id' => 'required|integer|exists:users,id',
            'title'       => 'required|string|min:5|max:255|unique:courses,title',
            'description' => 'required|string',
            'price'       => 'required|numeric|min:0',
            'category'    => 'nullable|string|max:50',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        return $this->validationError($validator);
    }
}
