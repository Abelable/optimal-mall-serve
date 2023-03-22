<?php

namespace App\Services\Media\ShortVideo;

use App\Models\ShortVideoLike;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class ShortVideoLikeService extends BaseService
{
    public function getLike($userId, $videoId)
    {
        return ShortVideoLike::query()->where('user_id', $userId)->where('video_id', $videoId)->first();
    }

    public function newLike($userId, $videoId)
    {
        $praise = ShortVideoLike::new();
        $praise->user_id = $userId;
        $praise->video_id = $videoId;
        $praise->save();
        return $praise;
    }

    public function pageList($userId, PageInput $input, $curVideoId = 0, $columns = ['*'])
    {
        $query = ShortVideoLike::query()->where('user_id', $userId);
        if ($curVideoId != 0) {
            $query = $query->orderByRaw("CASE WHEN video_id = " . $curVideoId . " THEN 0 ELSE 1 END");
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function likeUserIdsGroup($videoIds)
    {
        return ShortVideoLike::query()
            ->whereIn('video_id', $videoIds)
            ->select(['video_id', 'user_id'])
            ->get()
            ->groupBy('video_id')
            ->map(function ($fan) {
                return $fan->pluck('user_id')->toArray();
            });
    }

    public function deleteList($videoId)
    {
        return ShortVideoLike::query()->where('video_id', $videoId)->delete();
    }
}
