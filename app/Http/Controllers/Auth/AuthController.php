<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\Auth\RegisterRequests;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Auth\Events\Verified;
use App\Repositories\DeviceRepository;
use Illuminate\Support\Facades\Auth;



class AuthController extends Controller
{
    use ApiResponseTrait;

    protected $authService , $deviceRepo;

    public function __construct(AuthService $authService, DeviceRepository $deviceRepo)
    {
        $this->authService = $authService;
        $this->deviceRepo = $deviceRepo;
    }
    public function register(RegisterRequests $request)
    {
        try {
            $result = $this->authService->registerUser($request->validated());
            if (!$result) {
                return $this->apiResponse(null, 'Invalid credentials', 422);
            }

            return $this->apiResponse($result, 'User registered successfully', 200);
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {

            $result = $this->authService->loginUser($request->username, $request->password);
            
            
            if ($request->has('fcm_token') && $request->filled('fcm_token')) {
                $this->deviceRepo->updateOrCreateToken(
                    $result['user']->id,
                    $request->input('fcm_token'),
                    $request->input('device_type')
                );
            }
            if (!$result) {
                return $this->apiResponse(null, 'Invalid credentials',422);
            }

            return $this->apiResponse($result, 'Login successful',200);
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            $this->authService->logoutUser($request->user());
            $this->deviceRepo->deleteToken($request->user()->id, $request->input('fcm_token'));

            return $this->apiResponse(null, 'Logout successful',200);
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request)
    {
        try {
            $status = $this->authService->sendResetLink($request->validated());
            if ($status === Password::RESET_LINK_SENT) {
                return  $this->apiResponse(null, 'The reset link has been sent to your email.',200);
            }
            return  $this->apiResponse(null, 'Failed to send reset link.', 422);
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e, 500);
        }
    }

    public function reset(ResetPasswordRequest $request)
    {
        try {
            $status = $this->authService->resetPassword($request->validated());
            if ($status === Password::PASSWORD_RESET) {
                return  $this->apiResponse(null, 'Password has been reset successfully.',200);
            }
            return $this->apiResponse(null, 'Invalid reset link or expired.', 422);
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e, 500);
        }
    }

    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return $this->apiResponse(null, 'The email is already verified', 200);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return $this->apiResponse(null, 'Email verified', 200);
    }

    public function resend(Request $request)
    {
        try {
            if ($request->user()->hasVerifiedEmail()) {
                return $this->apiResponse(null, 'The email is already verified', 200);
            }

            $request->user()->sendEmailVerificationNotification();

            return $this->apiResponse(null, 'Verification link sent', 200);
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e, 500);
        }
    }
}
