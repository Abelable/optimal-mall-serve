<?php

namespace App\Services\Media\Live;

use App\Models\LiveRoom;
use App\Services\BaseService;
use App\Utils\Enums\LiveStatus;
use App\Utils\Inputs\LiveRoomInput;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\SearchPageInput;
use Illuminate\Support\Facades\Cache;

class LiveRoomService extends BaseService
{
    public function pageList(PageInput $input, $curRoomId, $columns = ['*'], $statusList = [1, 3])
    {
        $query = LiveRoom::query();
        if ($curRoomId != 0) {
            $query = $query->orderByRaw("CASE WHEN id = " . $curRoomId . " THEN 0 ELSE 1 END");
        }
        return $query
            ->whereIn('status', $statusList)
            ->orderByRaw("CASE WHEN status = 1 THEN 0 WHEN status = 3 THEN 1 WHEN status = 2 THEN 2 ELSE 3 END")
            ->orderBy('views', 'desc')
            ->orderBy('praise_number', 'desc')
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
