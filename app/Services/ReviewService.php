<?php

namespace App\Services;

use App\Repositories\ReviewRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use Exception;

class ReviewService
{
    protected $reviewRepo;

    public function __construct(ReviewRepository $reviewRepo)
    {
        $this->reviewRepo = $reviewRepo;
    }

    public function storeReview(array $data)
    {
        $data['user_id'] = Auth::id();  
        return $this->reviewRepo->create($data);
    }

    public function updateReview( array $data)
    {
        $review = $this->reviewRepo->findById($data['review_id']);
        $user = User::find(Auth::id());

        if ($user->id !== $review->user_id && !$user->hasRole('Admin')) {
            throw new Exception("Unauthorized. You can only update your own reviews.", 403);
        }

        return $this->reviewRepo->update( $data);
    }

    public function destroyReview($data)
    {
        $review = $this->reviewRepo->findById($data['review_id']);
        $user = User::find(Auth::id());

        if ($user->id !== $review->user_id && !$user->hasRole('Admin')) {
            throw new Exception("Unauthorized. You can only delete your own reviews.", 403);
        }

        return $this->reviewRepo->delete($data['review_id']);
    }

    public function getCourseReviews($request)
    {
        return $this->reviewRepo->getReviewsByCourseId($request['course_id'], $request['perPage'] ?? 10);
    }
}
