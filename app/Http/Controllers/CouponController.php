<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use App\Http\Requests\Coupon\StoreCouponRequest;
use App\Http\Requests\Coupon\CalculatePriceRequest;
use App\Traits\ApiResponseTrait;
use Exception;

class CouponController extends Controller
{
    use ApiResponseTrait;

    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    
    public function store(StoreCouponRequest $request)
    {
        try {
            $coupon = $this->couponService->createCoupon($request->validated());
            return self::apiResponse($coupon, 'Coupon created successfully', 201);
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), $e->getCode() ?: 400);
        }
    }

 
    public function calculate(CalculatePriceRequest $request)
    {
        try {
           
            $priceDetails = $this->couponService->calculateFinalPrice($request->validated());

            return self::apiResponse($priceDetails, 'Price calculated successfully before payment.');
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
