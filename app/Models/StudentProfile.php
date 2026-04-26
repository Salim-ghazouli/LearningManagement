<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_of_birth',
        'total_courses'
    ];

    /**
     * العودة للمستخدم صاحب الملف.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
