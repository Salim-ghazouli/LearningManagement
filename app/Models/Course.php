<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = ['instructor_id', 'title', 'description', 'price', 'is_free', 'category'];

    public function scopeFilter(Builder $query, array $filters)
    {
        return $query
            ->when($filters['title'] ?? null, function ($q, $title) {
                $q->where('courses.title', 'like', "%$title%");
            })
            ->when($filters['max_price'] ?? null, function ($q, $price) {
                $q->where('courses.price', '<=', $price);
            })
            ->when($filters['category'] ?? null, function ($q, $category) {
                $q->where('courses.category', $category);
            })
            ->when($filters['username'] ?? null, function ($q, $username) {
                $q->join('course_user', 'courses.id', '=', 'course_user.course_id')
                    ->join('users', 'course_user.user_id', '=', 'users.id')
                    ->where('users.username', 'like', "%$username%")
                    ->select('courses.*');
            })
            ->when($filters['instructor_id'] ?? null, function ($q, $instructor_id) {
                $q->join('course_user', 'courses.id', '=', 'course_user.course_id')
                    ->where('course_user.user_id', $instructor_id)
                    ->select('courses.*')
                    ->distinct();
            });
    }
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
    public function instructors()
    {
        return $this->belongsToMany(User::class, 'course_user');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('course_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png']);

        $this->addMediaCollection('files')
            ->acceptsMimeTypes(['application/pdf']);
    }
}
