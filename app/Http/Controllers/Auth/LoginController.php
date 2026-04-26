<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;

use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    use ApiResponseTrait;


    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function login(LoginRequest $request)

    {
        try {

            $result = $this->authService->loginUser($request->username, $request->password);

            if (!$result) {
                return $this->apiResponse(null, 'Invalid credentials', 401);
            }

            return $this->apiResponse($result, 'Login successful');
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e, 500);
        }
    }
}
