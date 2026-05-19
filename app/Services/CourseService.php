<?php

namespace App\Services;

use App\Repositories\CourseRepository;
use Exception;
use Illuminate\Support\Facades\Auth;

class CourseService
{
    protected $courseRepo;

    public function __construct(CourseRepository $courseRepo)
    {
        $this->courseRepo = $courseRepo;
    }

    public function createCourse(array $data)
    {
        try {
            
            if ($data['price'] == 0) {
                $data['is_free'] = true;
            }
            return $this->courseRepo->create($data);
        } catch (Exception $e) {
            throw new Exception("Error creating course: " . $e->getMessage());
        }
    }

    public function updateCourse(array $data)
    {
        try {
            $id = $data['id'] ?? null;

            $course = $this->courseRepo->findById($id);

            $isOwner = $course->instructors()->where('user_id', Auth::id())->exists();
            if ($data['price'] == 0) {
                $data['is_free'] = true;
            }

            return $this->courseRepo->update($course, $data);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function listCourses($filters)
    {
        try {
            return $this->courseRepo->getAll($filters);
        } catch (Exception $e) {
            throw new Exception("Could not fetch courses: " . $e->getMessage());
        }
    }
    public function deleteCourse($id)
    {
        try {
            $course = $this->courseRepo->findById($id);

          //  if ($course->instructor_id !== Auth::id()) {
            //    throw new Exception("You are not authorized to delete this course.", 403);
            //}

            return $this->courseRepo->delete($course);
        } catch (Exception $e) {
            throw $e;
        }
    }
    public function handleMediaUpload($data)
    {
        try {
            $images = $data['course_images'] ?? request()->file('course_images');
            $files = $data['course_files'] ?? request()->file('course_files');

            return $this->courseRepo->addMedia(
                $data['course_id'],
                $this->normalizeUploadedFiles($images),
                $this->normalizeUploadedFiles($files)
            );
        } catch (\Exception $e) {
            throw new \Exception("Media upload failed: " . $e->getMessage(), 500);
        }
    }

    public function updateCourseMedia($data)
    {
        try {
            $courseId = $data['course_id'];

            $this->courseRepo->clearAllMedia($courseId);

            return $this->handleMediaUpload($data);
        } catch (\Exception $e) {
            throw new \Exception("Media update failed: " . $e->getMessage(), 500);
        }
    }

    protected function normalizeUploadedFiles($uploadedFiles)
    {
        if (empty($uploadedFiles)) {
            return [];
        }

        if ($uploadedFiles instanceof \Illuminate\Http\UploadedFile) {
            return [$uploadedFiles];
        }

        if (is_array($uploadedFiles) || $uploadedFiles instanceof \Traversable) {
            return array_values($uploadedFiles);
        }

        return [$uploadedFiles];
    }

    public function removeMedia($id)
    {
        if (is_array($id) && isset($id['media_id'])) {
            $id = $id['media_id'];
        }

        try {
            return $this->courseRepo->deleteMedia($id);
        } catch (\Exception $e) {
            throw new \Exception("Could not delete file: " . $e->getMessage(), 404);
        }
    }
}
