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
        $user_id = $data['user_id'] ?? Auth::id();
        $course_id = $data['course_id'];

        if ($this->enrollmentRepo->checkEnrollmentExists($user_id, $course_id)) {
            throw new Exception("Duplicate enrollment detected. Student is already enrolled in this course.", 400);
        }
        $course = \App\Models\Course::findOrFail($course_id);

        if ($course->is_free || $course->price <= 0) {
            return $this->enrollmentRepo->enroll($user_id, $course_id, 'active');
        }
        $hasPaid = \App\Models\Transaction::where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->where('status', 'completed')
            ->exists();
        if (!$hasPaid) {
            throw new Exception("Access Denied. You must purchase the course via Stripe first before enrolling.", 402);
        }


        return $this->enrollmentRepo->enroll($user_id, $course_id, 'active');
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
