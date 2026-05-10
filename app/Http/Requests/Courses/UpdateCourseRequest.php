<?php

namespace App\Http\Requests\Courses;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;

class UpdateCourseRequest extends FormRequest
{
    use ApiResponseTrait;
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'title'       => 'sometimes|string|min:5|max:255|unique:courses,title,' . $this->route('id'),
            'description' => 'sometimes|string',
            'price'       => 'sometimes|numeric|min:0',
            'category'    => 'sometimes|string|max:50',
            'id'          => 'required|exists:courses,id',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        return $this->validationError($validator);
    }
}
