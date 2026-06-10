<?php

namespace App\Services;

use App\Repositories\CouponRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;

class CouponService
{
    protected $couponRepo;

    public function __construct(CouponRepository $couponRepo)
    {
        $this->couponRepo = $couponRepo;
    }

    
    public function createCoupon(array $data)
    {
        $user = User::find(Auth::id()); 
        if (!$user->hasRole('Admin')) {
            throw new Exception("Unauthorized. Only admins can create coupons.", 403);
        }
        return $this->couponRepo->create($data);
    }

    
    public function calculateFinalPrice(array $data)
    {
        $courseId = $data['course_id'];
        $couponCode = $data['coupon_code'] ?? null;

        $course = $this->couponRepo->findCourseById($courseId);

        if ($course->is_free || $course->price <= 0) {
            return [
                'original_price' => 0,
                'discount_amount' => 0,
                'final_price' => 0,
                'coupon_applied' => false
            ];
        }

        $originalPrice = $course->price;
        $discountAmount = 0;
        $couponApplied = false;

        if ($couponCode) {
            $coupon = $this->couponRepo->findByCode($couponCode);

            if (!$coupon) {
                throw new Exception("Invalid or inactive coupon code.", 422);
            }

            if ($coupon->expires_at && $coupon->expires_at->isPast()) {
                throw new Exception("This coupon has expired.", 422);
            }

            if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
                throw new Exception("This coupon has reached its maximum usage limit.", 422);
            }

            $userId = Auth::id();
            if ($this->couponRepo->hasUserUsedCoupon($coupon->id, $userId)) {
                throw new Exception("You have already used this coupon before.", 422);
            }

            if ($coupon->type === 'percentage') {
                $discountAmount = ($originalPrice * $coupon->value) / 100;
            } elseif ($coupon->type === 'fixed') {
                $discountAmount = $coupon->value;
            }

            if ($discountAmount > $originalPrice) {
                $discountAmount = $originalPrice;
            }

            $couponApplied = true;
        }

        $finalPrice = $originalPrice - $discountAmount;

        return [
            'original_price'  => round($originalPrice, 2),
            'discount_amount' => round($discountAmount, 2),
            'final_price'     => round($finalPrice, 2),
            'coupon_applied'  => $couponApplied,
            'coupon_id'       => $couponApplied ? $coupon->id : null
        ];
    }

    
    public function applyCouponUsage($couponId)
    {
        return $this->couponRepo->logCouponUsage($couponId, Auth::id());
    }
}
