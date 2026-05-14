<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CourseService;
use App\Traits\ApiResponseTrait;
use App\Http\Requests\Media\UploadCourseMediaRequest;
use App\Http\Requests\Media\DeleteCourseMediaRequest;




class CourseMediaController extends Controller
{
    protected $courseService;
    use ApiResponseTrait;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function upload(UploadCourseMediaRequest $request)
    {
        try {
            $course = $this->courseService->handleMediaUpload($request->validated());

            return $this->apiResponse($course, 'Media uploaded successfully');
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), $e->getCode());
        }
    }

    public function updateMedia(UploadCourseMediaRequest $request)
    {
        try {
            $result = $this->courseService->updateCourseMedia($request->validated());

            return $this->apiResponse($result, 'Media updated successfully');
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }

    public function destroyMedia(DeleteCourseMediaRequest $request)
    {
        $mediaId = $request->validated()['media_id'] ?? $request->route('media_id');

        try {
            $this->courseService->removeMedia($mediaId);

            return $this->apiResponse(null, 'File deleted successfully');
        } catch (\Exception $e) {
            return $this->apiResponse(null, $e->getMessage(), 500);
        }
    }
}
