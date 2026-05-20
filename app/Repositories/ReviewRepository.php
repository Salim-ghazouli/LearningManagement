<?php

namespace App\Repositories;

use App\Models\Review;

class ReviewRepository
{
    public function create(array $data)
    {
        return Review::create($data);
    }

    public function update( array $data)
    {
        $review = Review::findOrFail($data['review_id']);
        $review->update($data);
        return $review;
    }

    public function findById($id)
    {
        return Review::findOrFail($id);
    }

    public function delete($review_id)
    {
        $review = Review::findOrFail($review_id);
        return $review->delete();
    }

    public function getReviewsByCourseId($course_id, $perPage = 10)
    {
        return Review::where('course_id', $course_id)->with('user')->latest()->paginate($perPage);
    }
}
