<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    use ApiResponseTrait;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        try {
            $status = $this->authService->sendResetLink($request->validated());
            if ($status === Password::RESET_LINK_SENT) {
                return  $this->apiResponse(null, 'The reset link has been sent to your email.');
            }
            return  $this->apiResponse(null, 'Failed to send reset link.', 400);
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e, 500);
        }
    }

    public function reset(ResetPasswordRequest $request)
    {
        try {
            $status = $this->authService->resetPassword($request->validated());
            if ($status === Password::PASSWORD_RESET) {
                return  $this->apiResponse(null, 'Password has been reset successfully.');
            }
            return $this->apiResponse(null, 'Invalid reset link or expired.', 400);
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e, 500);
        }
    }
}
