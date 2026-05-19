<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Lesson extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['course_id', 'title', 'description', 'is_free_preview', 'sort_order'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    
}
