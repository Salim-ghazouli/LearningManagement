<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Course extends Model
{
    protected $fillable = ['instructor_id', 'title', 'description', 'price', 'is_free', 'category'];

    public function scopeFilter(Builder $query, array $filters)
    {
        return $query
            ->when($filters['title'] ?? null, function ($q, $title) {
                $q->where('title', 'like', "%$title%");
            })
            ->when($filters['max_price'] ?? null, function ($q, $price) {
                $q->where('price', '<=', $price);
            })
            ->when($filters['category'] ?? null, function ($q, $category) {
                $q->where('category', $category);
            })
            ->when($filters['username'] ?? null, function ($q, $username) {
                $q->join('users', 'courses.instructor_id', '=', 'users.id')
                    ->where('users.username', 'like', "%$username%")
                    ->select('courses.*');
            })
            ->when($filters['instructor_id'] ?? null, function ($q, $instructor_id) {
                $q->where('instructor_id', $instructor_id);
            });
    }
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
