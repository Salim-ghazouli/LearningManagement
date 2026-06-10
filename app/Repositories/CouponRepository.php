<?php

namespace App\Repositories;

use App\Models\Coupon;
use App\Models\Course;

class CouponRepository
{
   
    public function create(array $data)
    {
        return Coupon::create($data);
    }

   
    public function findByCode($code)
    {
        return Coupon::where('code', $code)
            ->where('is_active', true)
            ->first();
    }

   
    public function findCourseById($courseId)
    {
        return Course::findOrFail($courseId);
    }

   
    public function logCouponUsage($couponId, $userId)
    {
        $coupon = Coupon::findOrFail($couponId);

        $coupon->increment('used_count');

        $coupon->users()->attach($userId, ['used_at' => now()]);

        return $coupon;
    }

    
    public function hasUserUsedCoupon($couponId, $userId)
    {
        $coupon = Coupon::findOrFail($couponId);
        return $coupon->users()->where('user_id', $userId)->exists();
    }
}
