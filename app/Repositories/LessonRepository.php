<?php

namespace App\Repositories;

use App\Models\Lesson;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class LessonRepository
{
    public function create(array $data)
    {
        $lesson = Lesson::create([
            'course_id'   => $data['course_id'],
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'order'       => $data['order'] ?? 1,
        ]);

        if (isset($data['files'])) {
            $this->uploadLessonFiles($lesson, $data['files']);
        }

        return $lesson->load('media');
    }

    public function update(array $data)
    {

        $lesson = Lesson::findOrFail($data['lesson_id']);
        $lesson->update($data);

        if (isset($data['files'])) {
            $this->uploadLessonFiles($lesson, $data['files']);
        }

        return $lesson->load('media');
    }

    public function findById($id)
    {
        return Lesson::with(['course', 'media'])->findOrFail($id);
    }

    public function delete($id)
    {
        $lesson = Lesson::findOrFail($id);

        $mediaItems = Media::where('model_id', $id)
            ->where('model_type', 'App\Models\Lesson')
            ->get();

        foreach ($mediaItems as $item) {
            $item->delete();
        }

        return $lesson->delete();
    }

    protected function uploadLessonFiles($lesson, $files)
    {
        $normalizedFiles = $this->normalizeUploadArray($files);

        $existingFileNames = $lesson->getMedia('lesson_materials')
            ->pluck('file_name')
            ->map(fn($name) => strtolower($name))
            ->toArray();

        foreach ($normalizedFiles as $file) {
            if (method_exists($file, 'getClientOriginalName')) {
                $originalName = $file->getClientOriginalName();
                $normalizedName = strtolower($originalName);

                if (in_array($normalizedName, $existingFileNames, true)) {
                    continue;
                }

                $existingFileNames[] = $normalizedName;
                $lesson->addMedia($file)->toMediaCollection('lesson_materials');
            }
        }
    }

    protected function normalizeUploadArray($uploads)
    {
        if (empty($uploads)) return [];
        if ($uploads instanceof \Illuminate\Http\UploadedFile) return [$uploads];
        if (is_array($uploads) || $uploads instanceof \Traversable) return array_values($uploads);
        return [$uploads];
    }
    
    public function getLessonsByCourseId($courseId)
    {
        return Lesson::where('course_id', $courseId)
            ->orderBy('order', 'asc') 
            ->with('media')             
            ->get();
    }
}
