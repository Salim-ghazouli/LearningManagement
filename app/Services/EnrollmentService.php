<?php

namespace App\Services;

use App\Repositories\EnrollmentRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use Exception;

class EnrollmentService
{
    protected $enrollmentRepo;
    protected $notificationService;

    public function __construct(EnrollmentRepository $enrollmentRepo, FirebaseNotificationService $notificationService)
    {
        $this->enrollmentRepo = $enrollmentRepo;
        $this->notificationService = $notificationService;
    }

    public function enrollStudent(array $data)
    {
        $user_id = $data['user_id'] ?? Auth::id();
        $course_id = $data['course_id'];

        if ($this->enrollmentRepo->checkEnrollmentExists($user_id, $course_id)) {
            throw new Exception("Duplicate enrollment detected. Student is already enrolled in this course.", 400);
        }

        $course = \App\Models\Course::with('instructors')->findOrFail($course_id);

        if (!$course->is_free && $course->price > 0) {
            $hasPaid = \App\Models\Transaction::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->where('status', 'completed')
                ->exists();

            if (!$hasPaid) {
                throw new Exception("Access Denied. You must purchase the course via Stripe first before enrolling.", 402);
            }
        }

        $re = $this->enrollmentRepo->enroll($user_id, $course_id, 'active');

        if ($re) {
            try {
                $student = \App\Models\User::findOrFail($user_id);

                $dataPayload = [
                    'course_id' => (string) $course_id,
                    'type' => 'course_enrollment'
                ];

                $this->notificationService->sendToUser(
                    $student->id,
                    " Enrollment Confirmed!",
                    "Hi {$student->name}, you have successfully enrolled in '{$course->title}'. Happy learning!",
                    $dataPayload
                );

                foreach ($course->instructors as $instructor) {
                    $this->notificationService->sendToUser(
                        $instructor->id,
                        " New Student Enrolled!",
                        "Great news! {$student->name} has just joined your course '{$course->title}'.",
                        $dataPayload
                    );
                }

                $admins = \App\Models\User::where('role', 'admin')->get();

                $instructorName = $course->instructors->first()?->name ?? 'Unknown';

                foreach ($admins as $admin) {
                    $this->notificationService->sendToUser(
                        $admin->id,
                        " New Platform Sale",
                        "Student '{$student->name}' has enrolled in '{$course->title}' by '{$instructorName}'.",
                        $dataPayload
                    );
                }
            } catch (\Exception $e) {
                throw new Exception("Notification sending failed: " . $e->getMessage());
            }
        }

        return $re;
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
