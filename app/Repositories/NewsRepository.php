<?php

namespace App\Repositories;

use App\Models\News;

class NewsRepository
{
    public function create(array $data)
    {
        return News::create($data);
    }

    public function update($new_id, array $data)
    {
        $news = News::findOrFail($new_id);
        $news->update($data);
        return $news;
    }

    public function findById($id)
    {
        return News::findOrFail($id);
    }

    public function delete($id)
    {
        $news = News::findOrFail($id);
        return $news->delete();
    }

    // جلب الأخبار مرتبة بناءً على المتطلبات (المتطلب رقم 4)
    public function getAllOrdered($perPage = 10)
    {
        return News::orderBy('order', 'asc')->paginate($perPage);
    }
}
