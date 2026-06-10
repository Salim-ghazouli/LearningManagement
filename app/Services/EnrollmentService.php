<?php

namespace App\Services;

use App\Repositories\EnrollmentRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use Exception;

class EnrollmentService
{
    protected $enrollmentRepo;

    public function __construct(EnrollmentRepository $enrollmentRepo)
    {
        $this->enrollmentRepo = $enrollmentRepo;
    }

    public function enrollStudent(array $data)
    {
        $userId = $data['user_id'] ?? Auth::id();
        $courseId = $data['course_id'];

        if ($this->enrollmentRepo->checkEnrollmentExists($userId, $courseId)) {
            throw new Exception("Duplicate enrollment detected. Student is already enrolled in this course.", 400);
        }

        return $this->enrollmentRepo->enroll($userId, $courseId, 'pending');
    }

    public function getStudentCourses($request)
    {
        $userId = $request['user_id'] ?? Auth::id();
        return $this->enrollmentRepo->getStudentCourses($userId);
    }

    public function getCourseStudents($request)
    {
        $courseId = $request['courseId'];
        return $this->enrollmentRepo->getCourseStudents($courseId);
    }

    public function changeEnrollmentStatus(array $data)
    {
        $user = User::find(Auth::id());  
        if ($user->hasRole('Student')) {
            throw new Exception("Unauthorized. Only Admins and Instructors can manage enrollment status.", 403);
        }

        return $this->enrollmentRepo->updateStatus($data['user_id'], $data['course_id'], $data['status']);
    }
}
