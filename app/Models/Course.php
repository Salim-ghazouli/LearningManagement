<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['instructor_id', 'title', 'description', 'price', 'is_free'];

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
    public function media()
    {
        return $this->morphMany(MediaFile::class, 'mediable');
    }
}
