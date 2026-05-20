<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\ReviewService;
use App\Http\Requests\Review\StoreReviewRequest;
use App\Http\Requests\Review\UpdateReviewRequest;
use App\Http\Requests\Review\DestroyReviewRequest;
use App\Http\Requests\Review\ShowReviewRequest;
use App\Traits\ApiResponseTrait;
use Exception;

class ReviewController extends Controller
{
    use ApiResponseTrait;

    protected $reviewService;

    public function __construct(ReviewService $reviewService)
    {
        $this->reviewService = $reviewService;
    }

    // 1. إنشاء تقييم جديد لكورس معين (طالب + مدرس + أدمن)
    public function create(StoreReviewRequest $request)
    {
        try {
            $review = $this->reviewService->storeReview($request->validated());
            return self::apiResponse($review, 'Review submitted successfully', 201);
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), $e->getCode() ?: 400);
        }
    }

    // 2. تحديث التقييم (فقط لصاحب التقييم أو الأدمن)
    public function update(UpdateReviewRequest $request)
    {
        try {
            $review = $this->reviewService->updateReview( $request->validated());
            return self::apiResponse($review, 'Review updated successfully');
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), $e->getCode() ?: 400);
        }
    }

    // 3. حذف التقييم (فقط لصاحب التقييم أو الأدمن)
    public function destroy(DestroyReviewRequest $request)
    {
        try {
            $this->reviewService->destroyReview($request->validated());
            return self::apiResponse(null, 'Review deleted successfully');
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), $e->getCode() ?: 400);
        }
    }

    // 4. جلب جميع مراجعات كورس معين (عام ومتاح للجميع)
    public function show_review_ByCourse(ShowReviewRequest $request)
    {
        try {
            $reviews = $this->reviewService->getCourseReviews($request->validated());
            return self::apiResponse($reviews, 'Course reviews retrieved successfully');
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), 500);
        }
    }
}
