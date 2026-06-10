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

        if ($user->hasRole('Student')) {
            $isEnrolled = $course->students()->where('user_id', $user->id)->exists();
            if (!$isEnrolled) {
                throw new \Exception("Access Denied. You must enroll in this course to view its lessons.", 403);
            }
        }

        return $this->lessonRepo->getLessonsByCourseId($courseId);
    }
}
