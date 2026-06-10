<?php

namespace App\Services;

use App\Repositories\NewsRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

use Exception;

class NewsService
{
    protected $newsRepo;

    public function __construct(NewsRepository $newsRepo)
    {
        $this->newsRepo = $newsRepo;
    }

    protected function checkAdmin()
    {
        $user = User::find(Auth::id());
        if (!$user->hasRole('Admin')) {
            throw new Exception("Access Denied. Only Admins can manage latest news.", 403);
        }
    }

    public function storeNews(array $data)
    {
        $this->checkAdmin();
        return $this->newsRepo->create($data);
    }

    public function updateNews(array $data)
    {
        $this->checkAdmin();
        return $this->newsRepo->update($data['new_id'],$data);
    }

    public function destroyNews($id)
    {
        $this->checkAdmin();
        return $this->newsRepo->delete($id);
    }

    public function getLatestNews($perPage = 10)
    {
        return $this->newsRepo->getAllOrdered($perPage);
    }
}
