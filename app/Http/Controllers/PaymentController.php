<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\StripeService;
use App\Http\Requests\Payment\CheckoutRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Exception;

class PaymentController extends Controller
{
    use ApiResponseTrait;
    protected $stripeService;

    public function __construct(StripeService $stripeService)
    {
        $this->stripeService = $stripeService;
    }

    public function checkout(CheckoutRequest $request)
    {
        try {
            $data = $this->stripeService->createCheckoutSession(
                $request->only(['course_id', 'coupon_code'])
            );
            return self::apiResponse($data, 'Payment link generated successfully. Please visit the checkout_url to pay.');
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), 400);
        }
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $this->stripeService->handleWebhookNotification($payload, $sigHeader);
            return response()->json(['status' => 'success'], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function success(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'Thank you! Your payment was successful and you are now enrolled in the course.',
            'session_id' => $request->query('session_id')
        ], 200);
    }

    public function cancel()
    {
        return response()->json([
            'status' => false,
            'message' => 'Payment was cancelled. You can try enrolling again whenever you are ready.'
        ], 200);
    }
}
