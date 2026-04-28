<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes,HasRoles;

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

   


    
    public function instructorProfile()
    {
        return $this->hasOne(InstructorProfile::class);
    }

   
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    
    public function uploadedMedia()
    {
        return $this->hasMany(MediaFile::class, 'uploaded_by');
    }
}
