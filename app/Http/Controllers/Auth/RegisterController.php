<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\Auth\RegisterRequests;

class RegisterController extends Controller
{
    use ApiResponseTrait;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function register(RegisterRequests $request)
    {
        try {
            $result = $this->authService->registerUser($request->validated());
            if (!$result) {
                return $this->apiResponse(null, 'Invalid credentials', 401);
            }

            return $this->apiResponse($result, 'User registered successfully', 201);
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e, 500);
        }
    }
}
