<?php

namespace App\Http\Requests\Courses;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;

class ShowCoursesRequest extends FormRequest
{
    use ApiResponseTrait;
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'title' => 'nullable|string|max:255',
            'max_price' => 'nullable|numeric|min:0',
            'per_page' => 'nullable|integer|min:1|max:100',
            'category' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'instructor_id' => 'nullable|integer|exists:users,id',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        return $this->validationError($validator);
    }
}
