<?php

namespace App\Services;

use App\Models\LiveRoom;
use App\Utils\Enums\LiveStatus;
use App\Utils\Inputs\LivePageInput;
use App\Utils\Inputs\Admin\LivePageInput as AdminLiveRoomInput;
use App\Utils\Inputs\LiveRoomInput;
use App\Utils\Inputs\SearchPageInput;
use Illuminate\Support\Facades\Cache;

class LiveRoomService extends BaseService
{
    public function pageList(LivePageInput $input, $columns = ['*'], $statusList = [1, 3])
    {
        $query = LiveRoom::query();
        if ($input->roomId != 0) {
            $query = $query->orderByRaw("CASE WHEN id = " . $input->roomId . " THEN 0 ELSE 1 END");
        }
        if ($input->status != 0) {
            $query = $query->where('status', $input->status);
        } else {
            $query = $query->whereIn('status', $statusList);
        }
        return $query
            ->orderByRaw("CASE WHEN status = 1 THEN 0 WHEN status = 3 THEN 1 WHEN status = 2 THEN 2 ELSE 3 END")
            ->orderBy('views', 'desc')
            ->orderBy('praise_number', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function adminPageList(AdminLiveRoomInput $input, $columns = ['*'])
    {
        $query = LiveRoom::query();
        if (!empty($input->status)) {
            $query = $query->where('status', $input->status);
        }
        if (!empty($input->title)) {
            $query = $query->where('title', 'like', "%$input->title%");
        }
        if (!empty($input->userId)) {
            $query = $query->where('user_id', $input->userId);
        }

        return $query
            ->orderByRaw("FIELD(status, 1) DESC")
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function search(SearchPageInput $input, $statusList = [1, 3])
    {
        // todo whereIn无效
        return LiveRoom::search($input->keywords)
            ->whereIn('status', $statusList)
            ->orderBy('views', 'desc')
            ->orderBy('praise_number', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, 'page', $input->page);
    }

    public function newLiveRoom($userId, LiveRoomInput $input)
    {
        $room = LiveRoom::new();
        $room->user_id = $userId;
        $room->title = $input->title;
        $room->cover = $input->cover;
        $room->share_cover = $input->shareCover;
        $room->direction = $input->direction;
        if (!empty($input->noticeTime)) {
            $room->status = LiveStatus::NOTICE;
            $room->notice_time = $input->noticeTime;
        }
        $room->save();
        return $room;
    }

    public function getUserRoom($userId, $statusList, $columns = ['*'])
    {
        $query = LiveRoom::query();
        return $query
            ->where('user_id', $userId)
            ->whereIn('status', $statusList)
            ->first($columns);
    }

    public function getRoom($id, $statusList, $columns = ['*'], $withGoodsList = false)
    {
        $query = LiveRoom::query();
        if ($withGoodsList) {
            $query = $query->with('goodsList');
        }
        return $query
            ->whereIn('status', $statusList)
            ->find($id, $columns);
    }

    public function getRoomById($id, $columns = ['*'])
    {
        return LiveRoom::query()->find($id, $columns);
    }

    public function getListByIds($ids, $columns = ['*'])
    {
        return LiveRoom::query()->whereIn('id', $ids)->get($columns);
    }

    public function cachePraiseNumber($roomId, $number)
    {
        return Cache::increment('live_room_praise_number' . $roomId, $number);
    }

    public function getPraiseNumber($roomId)
    {
        return Cache::get('live_room_praise_number' . $roomId) ?? 0;
    }

    public function clearPraiseNumber($roomId)
    {
        Cache::forget('live_room_praise_number' . $roomId);
    }

    public function cacheChatMsg($roomId, $msg)
    {
        $msgList = Cache::get('live_room_chat_msg_list' . $roomId) ?? [];
        if (count($msgList) >= 20) {
            array_shift($msgList);
        }
        $msgList[] = json_encode($msg);
        Cache::put('live_room_chat_msg_list' . $roomId, $msgList);
        return $msgList;
    }

    public function getChatMsgList($roomId)
    {
        $chatMsgList = Cache::get('live_room_chat_msg_list' . $roomId) ?? [];
        return collect($chatMsgList)->map(function ($msg) {
            return json_decode($msg);
        });
    }

    public function clearChatMsgList($roomId)
    {
        Cache::forget('live_room_chat_msg_list' . $roomId);
    }
}
