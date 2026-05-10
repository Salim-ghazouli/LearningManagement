<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class CourseRepository
{

    public function create(array $data)
    {
        $course = Course::create($data);
        $course->instructors()->attach(Auth::id());
        return $course;
    }

    public function findById($id)
    {
        return Course::findOrFail($id);
    }

    public function update($course, array $data)
    {
        $course->update($data);
        return $course;
    }

    public function delete($course)
    {
        return $course->delete();
    }
    public function getAll($filters = [])
    {
        $query = Course::query()->with('instructors:id,username');

        if (!empty($filters)) {
            $query->filter($filters);
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }
}
