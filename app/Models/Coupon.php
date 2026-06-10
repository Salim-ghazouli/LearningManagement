<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = ['code', 'type', 'value', 'usage_limit', 'used_count', 'expires_at', 'is_active'];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean'
    ];
    // علاقة لمعرفة المستخدمين الذين استعملوا هذا الكوبون (المتطلب رقم 6)
    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user')->withPivot('used_at');
    }
}
