<?php

namespace App\Http\Controllers\Auth;

use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    use ApiResponseTrait;


    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    public function logout(Request $request)
    {
        try {
            $this->authService->logoutUser($request->user());

            return $this->apiResponse(null, 'Logout successful');
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e, 500);
        }
    }
}
