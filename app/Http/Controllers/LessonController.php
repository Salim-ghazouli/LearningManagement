<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LessonService;
use App\Http\Requests\Lesson\CreateLessonRequest;
use App\Http\Requests\Lesson\UpdateLessonRequest;
use App\Http\Requests\Lesson\DestroyLessonRequest;
use App\Http\Requests\Lesson\GetLessonByCoursesRequest;
use App\Traits\ApiResponseTrait;

class LessonController extends Controller
{
    use ApiResponseTrait;

    protected $lessonService;

    public function __construct(LessonService $lessonService)
    {
        $this->lessonService = $lessonService;
    }

    public function create(CreateLessonRequest $request)
    {
        try {
            $lesson = $this->lessonService->storeLesson($request->validated());
            return self::apiResponse($lesson, 'Lesson created successfully', 201);
        } catch (\Exception $e) {
            return self::apiResponse(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function show(DestroyLessonRequest $request)
    {
        try {
            $lesson = $this->lessonService->getLessonDetails($request->validated()['lesson_id']);
            return self::apiResponse($lesson, 'Lesson retrieved successfully');
        } catch (\Exception $e) {
            return self::apiResponse(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

    public function update(UpdateLessonRequest $request)
    {
        try {
            $lesson = $this->lessonService->updateLesson($request->validated());
            return self::apiResponse($lesson, 'Lesson updated successfully');
        } catch (\Exception $e) {
            return self::apiResponse(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }

  
    public function destroy(DestroyLessonRequest $request)
    {
        try {
            $this->lessonService->destroyLesson($request->validated()['lesson_id']);
            return self::apiResponse(null, 'Lesson deleted successfully');
        } catch (\Exception $e) {
            return self::apiResponse(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
    public function getByCourse(GetLessonByCoursesRequest $request)
    {
        try {
            $lessons = $this->lessonService->getCourseLessons($request->validated()['course_id']);
            return self::apiResponse($lessons, 'Course lessons retrieved successfully and ordered.');
        } catch (\Exception $e) {
            return self::apiResponse(null, $e->getMessage(), $e->getCode() ?: 500);
        }
    }
}
