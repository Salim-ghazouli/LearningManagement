<?php

namespace App\Http\Requests\Auth;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Traits\ApiResponseTrait;

class RegisterRequests extends FormRequest
{
   
    use ApiResponseTrait;
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        return [
            'username' => 'required|string|unique:users,username',
            'full_name' => 'sometimes|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        return $this->validationError($validator);
    }
}
