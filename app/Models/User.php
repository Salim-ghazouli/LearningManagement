<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * الحقول القابلة للتعبئة (Fillable) بناءً على تخطيطك.
     */
    protected $fillable = [
        'role_id',
        'full_name',
        'username',
        'email',
        'password',
        'phone',
        'is_active',
        'last_login_at',
    ];

    /**
     * الحقول التي يجب إخفاؤها عند تحويل المودل إلى JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * الحقول التي يتم تحويل أنواعها تلقائياً.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع جدول الأدوار (Roles)[cite: 3].
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * العلاقة مع ملف تعريف المدرس (Instructor Profile).
     */
    public function instructorProfile()
    {
        return $this->hasOne(InstructorProfile::class);
    }

    /**
     * العلاقة مع ملف تعريف الطالب (Student Profile)[cite: 5].
     */
    public function studentProfile()
    {
        return $this->hasOne(StudentProfile::class);
    }

    /**
     * علاقة الميديا المرفوعة بواسطة المستخدم[cite: 6].
     */
    public function uploadedMedia()
    {
        return $this->hasMany(MediaFile::class, 'uploaded_by');
    }
}
