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

        // 1. فحص التكرار
        if ($this->enrollmentRepo->checkEnrollmentExists($user_id, $course_id)) {
            throw new Exception("Duplicate enrollment detected. Student is already enrolled in this course.", 400);
        }

        // 2. جلب بيانات الكورس مع المدرسين بناءً على اسم علاقتك: instructors
        $course = \App\Models\Course::with('instructors')->findOrFail($course_id);

        // 3. فحص هل الكورس مدفوع؟ إذا كان مدفوعاً، نتحقق من عملية الدفع
        if (!$course->is_free && $course->price > 0) {
            $hasPaid = \App\Models\Transaction::where('user_id', $user_id)
                ->where('course_id', $course_id)
                ->where('status', 'completed')
                ->exists();

            if (!$hasPaid) {
                throw new Exception("Access Denied. You must purchase the course via Stripe first before enrolling.", 402);
            }
        }

        // 4. تنفيذ عملية التسجيل الفعلية
        $re = $this->enrollmentRepo->enroll($user_id, $course_id, 'active');

        if ($re) {
            try {
                // 5. جلب بيانات الطالب
                $student = \App\Models\User::findOrFail($user_id);

                $dataPayload = [
                    'course_id' => (string) $course_id,
                    'type' => 'course_enrollment'
                ];

                // المتطلب 1: إشعار الطالب
                $this->notificationService->sendToUser(
                    $student->id,
                    "🎉 Enrollment Confirmed!",
                    "Hi {$student->name}, you have successfully enrolled in '{$course->title}'. Happy learning!",
                    $dataPayload
                );

                // المتطلب 2: إرسال إشعار لكل مدرس في هذا الكورس (لأن العلاقة Many-to-Many)
                foreach ($course->instructors as $instructor) {
                    $this->notificationService->sendToUser(
                        $instructor->id,
                        "👨‍🏫 New Student Enrolled!",
                        "Great news! {$student->name} has just joined your course '{$course->title}'.",
                        $dataPayload
                    );
                }

                // المتطلب 3: إشعار الأدمن
                $admins = \App\Models\User::where('role', 'admin')->get();

                // نأخذ اسم أول مدرس للعرض في إشعار الأدمن كمثال
                $instructorName = $course->instructors->first()?->name ?? 'Unknown';

                foreach ($admins as $admin) {
                    $this->notificationService->sendToUser(
                        $admin->id,
                        "💼 New Platform Sale",
                        "Student '{$student->name}' has enrolled in '{$course->title}' by '{$instructorName}'.",
                        $dataPayload
                    );
                }
            } catch (\Exception $e) {
                // Log the error but don't fail the enrollment process
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
