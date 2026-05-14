<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Courses\StoreCourseRequest;
use App\Http\Requests\Courses\ShowCoursesRequest;
use App\Http\Requests\Courses\UpdateCourseRequest;
use App\Http\Requests\Courses\DeleteCourseRequest;
use App\Services\CourseService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    use ApiResponseTrait;

    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function create_course(StoreCourseRequest $request)
    {
        try {
            $course = $this->courseService->createCourse($request->validated());

            return $this->apiResponse($course, 'Course created successfully', 200);
        } catch (Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), 400);
        }
    }

    public function Show_courses(ShowCoursesRequest $request)
    {
        try {
            $courses = $this->courseService->listCourses($request->all());

            return $this->apiResponse($courses, 'Courses retrieved successfully', 200);
        } catch (Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }
    public function update(UpdateCourseRequest $request)
    {
        try {
            $course = $this->courseService->updateCourse($request->all());

            return $this->apiResponse($course, 'Course updated successfully', 200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(), 500);
        }
    }
    public function delete(DeleteCourseRequest $request)
    {
        try {
            $courseId = $request->validated()['course_id'] ?? $request->route('course_id');
            $this->courseService->deleteCourse($courseId);

            return $this->apiResponse(null, 'Course deleted successfully', 200);
        } catch (Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }

    public function getMyCourses(ShowCoursesRequest $request)
    {
        try {
            $filters = $request->only(['title', 'category', 'per_page']);
            $filters['instructor_id'] = Auth::id();

            $courses = $this->courseService->listCourses($filters);

            return $this->apiResponse($courses, 'Your courses retrieved successfully', 200);
        } catch (\Exception $e) {
            return $this->apiResponse(null, "Failed to fetch your courses: " . $e->getMessage(), 500);
        }
    }
}
