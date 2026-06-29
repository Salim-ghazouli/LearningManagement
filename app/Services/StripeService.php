<?php

namespace App\Services;

use App\Repositories\TransactionRepository;
use App\Services\CouponService;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\Auth;
use Exception;

class StripeService
{
    protected $transactionRepo;
    protected $couponService;

    public function __construct(TransactionRepository $transactionRepo, CouponService $couponService)
    {
        $this->transactionRepo = $transactionRepo;
        $this->couponService = $couponService;

        // إعداد مفتاح السري لـ Stripe المأخوذ من الـ .env أو config
        Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));
    }

    public function createCheckoutSession(array $data)
    {
        $user = Auth::user();
        if (!$data['coupon_code']) {
            $data['coupon_code'] = null; 
        }
        $priceDetails = $this->couponService->calculateFinalPrice($data);
        $finalPrice = $priceDetails['final_price'];

        if ($finalPrice <= 0) {
            throw new Exception("This course is free, you can enroll directly without payment.", 400);
        }

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Course Enrollment',
                        'description' => 'Payment for Course ID: ' . $data['course_id'],
                    ],
                    'unit_amount' => $finalPrice * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => url('/api/payment/success?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => url('/api/payment/cancel'),
        ]);

        $this->transactionRepo->createTransaction([
            'user_id' => $user->id,
            'course_id' => $data['course_id'],
            'stripe_session_id' => $session->id,
            'amount' => $finalPrice,
            'status' => 'completed'
        ]);

        if ($priceDetails['coupon_applied']) {
            $this->couponService->applyCouponUsage($priceDetails['coupon_id']);
        }

        return [
            'checkout_url' => $session->url,
            'session_id' => $session->id,
            'amount_to_pay' => $finalPrice
        ];
    }

    public function handleWebhookNotification($payload, $sigHeader)
    {
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            throw new Exception("Invalid payload", 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            throw new Exception("Invalid signature", 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $this->transactionRepo->completePaymentAndEnroll($session->id);
        }
    }
}
