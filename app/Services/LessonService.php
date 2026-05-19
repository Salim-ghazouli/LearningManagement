<?php

namespace App\Services;

use App\Repositories\LessonRepository;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;

class LessonService
{
    protected $lessonRepo;

    public function __construct(LessonRepository $lessonRepo)
    {
        $this->lessonRepo = $lessonRepo;
    }

    // شروط الإضافة: الأدمن للكل، والمدرس لكورساته فقط (المتطلب 1 و 2 و 3)
    public function storeLesson(array $data)
    {
        $user = User::find(Auth::id());
        $course = Course::findOrFail($data['course_id']);

        if ($user->hasRole('Instructor')) {
            $isOwner = $course->instructors()->where('user_id', $user->id)->exists();
            if (!$isOwner) {
                throw new Exception("Unauthorized. You can only add lessons to your own courses.", 403);
            }
        }

        return $this->lessonRepo->create($data);
    }

    // شروط التعديل: الأدمن أو المدرس المالك للكورس (المتطلب 1)
    public function updateLesson(array $data)
    {
        $user = User::find(Auth::id());
        $lesson = $this->lessonRepo->findById($data['lesson_id']);
        $course = $lesson->course;

        if ($user->hasRole('Instructor')) {
            $isOwner = $course->instructors()->where('user_id', $user->id)->exists();
            if (!$isOwner) {
                throw new Exception("Unauthorized. You can only update lessons in your own courses.", 403);
            }
        }

        return $this->lessonRepo->update($data);
    }

    // جلب الدرس وفحص الـ Enrollment لمنع الوصول غير المصرح به (المتطلب 5)
    public function getLessonDetails($id)
    {
        $user = User::find(Auth::id());
        $lesson = $this->lessonRepo->findById($id);
        $course = $lesson->course;

        if ($user->hasRole('Student')) {
            $isEnrolled = $course->students()->where('user_id', $user->id)->exists();
            if (!$isEnrolled) {
                throw new Exception("Access Denied. You must be enrolled in this course to view this lesson.", 403);
            }
        }

        return $lesson;
    }

    // شروط الحذف: الأدمن أو المدرس المالك للكورس (المتطلب 1)
    public function destroyLesson($id)
    {
        $user = User::find(Auth::id());
        $lesson = $this->lessonRepo->findById($id);
        $course = $lesson->course;

        if ($user->hasRole('Instructor')) {
            $isOwner = $course->instructors()->where('user_id', $user->id)->exists();
            if (!$isOwner) {
                throw new Exception("Unauthorized. You can only delete lessons from your own courses.", 403);
            }
        }

        return $this->lessonRepo->delete($id);
    }
    public function getCourseLessons($courseId)
    {
        $user = User::find(Auth::id());
        $course = Course::findOrFail($courseId);

        // حماية المحتوى: إذا كان مستخدم عادي/طالب، نمنع الوصول لو مش مسجل (المتطلب 5)
        if ($user->hasRole('Student')) {
            $isEnrolled = $course->students()->where('user_id', $user->id)->exists();
            if (!$isEnrolled) {
                throw new \Exception("Access Denied. You must enroll in this course to view its lessons.", 403);
            }
        }

        // إذا كان أدمن أو مدرس أو طالب مسجل، يمرر الطلب بنجاح
        return $this->lessonRepo->getLessonsByCourseId($courseId);
    }
}
