<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'value',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
        'description',
        'is_global',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'is_global' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'coupon_user')->withPivot('used_at')->withTimestamps();
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'coupon_course')->withTimestamps();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    public function isUsageLimitReached(): bool
    {
        return $this->usage_limit && $this->used_count >= $this->usage_limit;
    }

    public function canBeUsed(): bool
    {
        return $this->is_active && !$this->isExpired() && !$this->isUsageLimitReached();
    }
}
