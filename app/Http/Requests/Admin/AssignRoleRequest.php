<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Contracts\Validation\Validator;

class AssignRoleRequest extends FormRequest
{
    use ApiResponseTrait;
    public function authorize(): bool
    {
        return true;
    }

   
    public function rules(): array
    {
        return [
            'user_id'   => 'required|exists:users,id',
            'role_name' => 'required|exists:roles,name',
        ];
        
    }
    protected function failedValidation(Validator $validator)
    {
        return $this->validationError($validator);
    }
}
