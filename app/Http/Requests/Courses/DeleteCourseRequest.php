<?php

namespace App\Http\Requests\Courses;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;

class DeleteCourseRequest extends FormRequest
{

    use ApiResponseTrait;
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'id' => 'required|exists:courses,id',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        return $this->validationError($validator);
    }
}
