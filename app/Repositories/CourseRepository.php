<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class CourseRepository
{

    public function create(array $data)
    {
        $course = Course::create($data);
        $course->instructors()->attach(Auth::id());
        return $course;
    }

    public function findById($id)
    {
        return Course::findOrFail($id);
    }

    public function update($course, array $data)
    {
        $course->update($data);
        return $course;
    }

    public function delete($course)
    {
        foreach ($course->getMedia() as $media) {
            $media->delete();
        }

        return $course->delete();
    }
    public function getAll($filters = [])
    {
        $query = Course::query()->with('instructors:id,username');

        if (!empty($filters)) {
            $query->filter($filters);
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }
    public function addMedia($courseId, $images = [], $files = [])
    {
        $course = Course::findOrFail($courseId);

        $images = $this->normalizeUploadArray($images);
        $files = $this->normalizeUploadArray($files);

        $existingImageNames = $course->getMedia('course_images')
            ->pluck('file_name')
            ->map(fn($name) => strtolower($name))
            ->toArray();

        $existingFileNames = $course->getMedia('files')
            ->pluck('file_name')
            ->map(fn($name) => strtolower($name))
            ->toArray();

        $skippedImages = [];
        $skippedFiles = [];

        if (!empty($images)) {
            foreach ($images as $image) {
                if (!method_exists($image, 'getClientOriginalName')) {
                    continue;
                }

                $originalName = $image->getClientOriginalName();
                $normalizedName = strtolower($originalName);

                if (in_array($normalizedName, $existingImageNames, true)) {
                    $skippedImages[] = $originalName;
                    continue;
                }

                $existingImageNames[] = $normalizedName;
                $course->addMedia($image)->toMediaCollection('course_images');
            }
        }

        if (!empty($files)) {
            foreach ($files as $file) {
                if (!method_exists($file, 'getClientOriginalName')) {
                    continue;
                }

                $originalName = $file->getClientOriginalName();
                $normalizedName = strtolower($originalName);

                if (in_array($normalizedName, $existingFileNames, true)) {
                    $skippedFiles[] = $originalName;
                    continue;
                }

                $existingFileNames[] = $normalizedName;
                $course->addMedia($file)->toMediaCollection('files');
            }
        }

        return [
            'course' => $course->load('media'),
            'skipped_images' => $skippedImages,
            'skipped_files' => $skippedFiles,
        ];
    }

    protected function normalizeUploadArray($uploads)
    {
        if (empty($uploads)) {
            return [];
        }

        if ($uploads instanceof \Illuminate\Http\UploadedFile) {
            return [$uploads];
        }

        if (is_array($uploads) || $uploads instanceof \Traversable) {
            return array_values($uploads);
        }

        return [$uploads];
    }

    public function clearMedia($courseId, $collectionName)
    {
        $course = Course::findOrFail($courseId);
        return $course->clearMediaCollection($collectionName);
    }

    public function clearAllMedia($courseId)
    {
        $course = Course::findOrFail($courseId);

        $mediaItems = Media::where('model_id', $courseId)
            ->where('model_type', 'App\Models\Course')
            ->get();

        foreach ($mediaItems as $item) {
            $item->delete(); // هذا سيحذف السجل والملف الفيزيائي من الـ Storage
        }
    }

    public function deleteMedia($id)
    {
        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::find($id);
        if (!$media) {
            $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('uuid', $id)->first();
        }
        if (!$media) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Media item not found for id: {$id}");
        }
        return $media->delete();
    }
}
