<?php

namespace App\Services;

use App\Repositories\CourseRepository;
use Exception;
use Illuminate\Support\Facades\Auth;

class CourseService
{
    protected $courseRepo;

    public function __construct(CourseRepository $courseRepo)
    {
        $this->courseRepo = $courseRepo;
    }

    public function createCourse(array $data)
    {
        try {
            $data['instructor_id'] = Auth::id();
            if ($data['price'] == 0) {
                $data['is_free'] = true;
            }
            return $this->courseRepo->create($data);
        } catch (Exception $e) {
            throw new Exception("Error creating course: " . $e->getMessage());
        }
    }

    public function updateCourse(array $data)
    {
        try {
            $id = $data['id'] ?? null;

            $course = $this->courseRepo->findById($id);

            if ($course->instructor_id !== Auth::id()) {
                throw new Exception("Unauthorized action.", 403);
            }
            if ($data['price'] == 0) {
                $data['is_free'] = true;
            }

            return $this->courseRepo->update($course, $data);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function listCourses($filters)
    {
        try {
            return $this->courseRepo->getAll($filters);
        } catch (Exception $e) {
            throw new Exception("Could not fetch courses: " . $e->getMessage());
        }
    }
    public function deleteCourse($id)
    {
        try {
            $course = $this->courseRepo->findById($id);

            if ($course->instructor_id !== Auth::id()) {
                throw new Exception("You are not authorized to delete this course.", 403);
            }

            return $this->courseRepo->delete($course);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
