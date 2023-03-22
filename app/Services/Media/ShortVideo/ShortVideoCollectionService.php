<?php

namespace App\Services\Media\ShortVideo;

use App\Models\ShortVideoCollection;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class ShortVideoCollectionService extends BaseService
{
     public function getCollection($userId, $videoId)
     {
         return ShortVideoCollection::query()->where('video_id', $videoId)->where('user_id', $userId)->first();
     }

     public function newCollection($userId, $videoId)
     {
        $collection = ShortVideoCollection::new();
        $collection->user_id = $userId;
        $collection->video_id = $videoId;
        $collection->save();
        return $collection;
     }

     public function pageList($userId, PageInput $input, $columns = ['*'])
     {
         return ShortVideoCollection::query()
             ->where('user_id', $userId)
             ->orderBy($input->sort, $input->order)
             ->paginate($input->limit, $columns, 'page', $input->page);
     }

    public function collectedUserIdsGroup($videoIds)
    {
        return ShortVideoCollection::query()
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
        return ShortVideoCollection::query()->where('video_id', $videoId)->delete();
    }
}
