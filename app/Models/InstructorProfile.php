<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'linkedin',
        'total_students',
        'total_courses'
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
