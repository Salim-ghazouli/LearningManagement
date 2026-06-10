<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\EnrollmentService;
use App\Http\Requests\Enrollment\StoreEnrollmentRequest;
use App\Http\Requests\Enrollment\UpdateStatusRequest;
use App\Http\Requests\Enrollment\CourseStudentsRequest;
use App\Http\Requests\Enrollment\StudentsEnrollCoursesRequest;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    use ApiResponseTrait;

    protected $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    // 1. تسجيل طالب في كورس (المتطلب رقم 1)    
    public function enroll(StoreEnrollmentRequest $request)
    {
        try {
            $enrollment = $this->enrollmentService->enrollStudent($request->validated());
            return self::apiResponse($enrollment, 'Enrollment request submitted successfully.', 201);
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), $e->getCode() ?: 400);
        }
    }

    // 2. جلب الكورسات التي سجل فيها الطالب الحالي (المتطلب رقم 3)
    public function myCourses(StudentsEnrollCoursesRequest $request)
    {
        try {
            $courses = $this->enrollmentService->getStudentCourses($request->validated());
            return self::apiResponse($courses, 'Enrolled courses retrieved successfully.');
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), 500);
        }
    }

    // 3. جلب الطلاب المسجلين في كورس معين (المتطلب رقم 4)
    public function courseStudents(CourseStudentsRequest $request)
    {
        try {
            $students = $this->enrollmentService->getCourseStudents($request->validated());
            return self::apiResponse($students, 'Course students retrieved successfully.');
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), 500);
        }
    }

    // 4. إدارة وتحديث حالة التسجيل (المتطلب رقم 5)
    public function updateStatus(UpdateStatusRequest $request)
    {
        try {
            $updatedEnrollment = $this->enrollmentService->changeEnrollmentStatus($request->validated());
            return self::apiResponse($updatedEnrollment, 'Enrollment status updated successfully.');
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
