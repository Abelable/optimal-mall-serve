<?php

namespace App\Services;

use App\Models\OrderKeyword;
use Illuminate\Support\Facades\DB;

class OrderKeywordService extends BaseService
{
    public function getHotList()
    {
        return OrderKeyword::query()
            ->select('content', DB::raw('count(*) as count'))
            ->groupBy('content')
            ->orderByDesc('count')
            ->take(10)
            ->get();
    }

    public function getListByUserId($userId, $columns = ['*'])
    {
        return OrderKeyword::query()->where('user_id', $userId)->orderBy('created_at', 'desc')->get($columns);
    }

    public function clearUserKeywords($userId)
    {
        OrderKeyword::query()->where('user_id', $userId)->delete();
    }

    public function addKeyword($userId, $content)
    {
        $keyword = OrderKeyword::query()->where('user_id', $userId)->where('content', $content)->first();
        if (!is_null($keyword)) {
            $keyword->delete();
        }

        $keyword = OrderKeyword::new();
        $keyword->user_id = $userId;
        $keyword->content = $content;
        $keyword->save();
    }
}
