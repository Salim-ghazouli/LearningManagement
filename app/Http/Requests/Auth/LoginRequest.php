<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Traits\ApiResponseTrait;

class LoginRequest extends FormRequest
{
    use ApiResponseTrait;
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string',
            'password' => 'required|confirmed',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        return $this->validationError($validator);
    }
    
}
