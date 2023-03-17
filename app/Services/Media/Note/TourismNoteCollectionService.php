<?php

namespace App\Services\Media\Note;

use App\Models\TourismNoteCollection;
use App\Services\BaseService;
use App\Utils\Inputs\PageInput;

class TourismNoteCollectionService extends BaseService
{
     public function getCollection($userId, $noteId)
     {
         return TourismNoteCollection::query()->where('video_id', $noteId)->where('user_id', $userId)->first();
     }

     public function newCollection($userId, $noteId)
     {
        $collection = TourismNoteCollection::new();
        $collection->user_id = $userId;
        $collection->note_id = $noteId;
        $collection->save();
        return $collection;
     }

    public function pageList($userId, PageInput $input, $columns = ['*'])
    {
        return TourismNoteCollection::query()
            ->where('user_id', $userId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }
}
