<?php

namespace App\Repositories;

use App\Models\Course;

class CourseRepository
{

    public function create(array $data)
    {
        return Course::create($data);
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
        $query = Course::query()->with('instructor:id,username');

        if (!empty($filters)) {
            $query->filter($filters);
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }
}
