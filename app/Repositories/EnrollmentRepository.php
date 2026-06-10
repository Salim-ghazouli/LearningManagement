<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class EnrollmentRepository
{
    public function enroll($userId, $courseId, $status = 'pending')
    {
        $user = User::findOrFail($userId);
        $user->enrollments()->attach($courseId, ['status' => $status]);
        return $this->getEnrollmentDetails($userId, $courseId);
    }

    public function checkEnrollmentExists($userId, $courseId)
    {
        return DB::table('enrollments')
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();
    }

    public function getStudentCourses($userId)
    {
        $user = User::findOrFail($userId);
        return $user->enrollments;
    }

    public function getCourseStudents($courseId)
    {
        $course = Course::findOrFail($courseId);
        return $course->students;
    }

    public function updateStatus($userId, $courseId, $status)
    {
        $user = User::findOrFail($userId);
        $user->enrollments()->updateExistingPivot($courseId, ['status' => $status]);
        return $this->getEnrollmentDetails($userId, $courseId);
    }

    public function getEnrollmentDetails($userId, $courseId)
    {
        return DB::table('enrollments')
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
    }
}
