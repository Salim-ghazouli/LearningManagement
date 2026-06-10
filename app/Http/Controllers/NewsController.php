<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\NewsService;
use App\Http\Requests\News\StoreNewsRequest;
use App\Http\Requests\News\UpdateNewsRequest;
use App\Http\Requests\News\DestroyNewsRequest;
use App\Http\Requests\News\ShowNewsRequest;
use App\Traits\ApiResponseTrait;

use Exception;

class NewsController extends Controller
{
    use ApiResponseTrait;

    protected $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    public function store(StoreNewsRequest $request)
    {
        try {
            $news = $this->newsService->storeNews($request->validated());
            return self::apiResponse($news, 'News created successfully', 201);
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function update(UpdateNewsRequest $request)
    {
        try {
            $news = $this->newsService->updateNews($request->validated());
            return self::apiResponse($news, 'News updated successfully');
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function destroy(DestroyNewsRequest $request)
    {
        try {
            $this->newsService->destroyNews($request->validated()['new_id']);
            return self::apiResponse(null, 'News deleted successfully');
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), $e->getCode() ?: 400);
        }
    }

    public function index(ShowNewsRequest $request)
    {
        try {
            $news = $this->newsService->getLatestNews($request->validated()['perPage'] ?? 10);
            return self::apiResponse($news, 'Latest news retrieved successfully and ordered.');
        } catch (Exception $e) {
            return self::apiResponse(null, $e->getMessage(), 500);
        }
    }
}
