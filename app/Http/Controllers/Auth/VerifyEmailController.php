<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponseTrait;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    use ApiResponseTrait;


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
                return $this->apiResponse(null, 'The email is already verified', 400);
            }

            $request->user()->sendEmailVerificationNotification();

            return $this->apiResponse(null, 'Verification link sent', 200);
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e, 500);
        }
    }
}
