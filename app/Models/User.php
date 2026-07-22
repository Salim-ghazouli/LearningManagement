<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Filament\Panel;
use Filament\Models\Contracts\HasName;


class User extends Authenticatable implements MustVerifyEmail, HasName
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $guard_name = 'api';
    protected $fillable = [
        'full_name',
        'username',
        'email',
        'password',
        'phone',
        'is_active',
        'last_login_at',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];





    public function teachingCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user');
    }


    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }


    public function uploadedMedia()
    {
        return $this->hasMany(MediaFile::class, 'uploaded_by');
    }
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user', 'user_id', 'course_id')
            ->withTimestamps();
    }
    public function enrollments()
    {
        return $this->belongsToMany(Course::class, 'enrollments', 'user_id', 'course_id')
            ->withPivot('status')
            ->withTimestamps();
    }
    public function devices()
    {
        return $this->hasMany(UserDevice::class);
    }
    //public function canAccessPanel(Panel $panel): bool
    //{

    //  return $this->role === 'admin';
    //}
    public function getFilamentAuthIdentifierName(): string
    {
        return 'username';
    }

    public function getFilamentName(): string
    {
        return $this->username ?? $this->name ?? 'Admin';
    }

    public function getRoleAttribute(): ?string
    {
        return $this->roles->pluck('name')->first();
    }
}
