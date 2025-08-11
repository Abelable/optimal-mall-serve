<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\LiveGoods;
use App\Models\LiveRoom;
use App\Services\FanService;
use App\Services\GoodsService;
use App\Services\Media\Live\LiveGoodsService;
use App\Services\Media\Live\LiveRoomService;
use App\Utils\CodeResponse;
use App\Utils\Enums\LiveGroupMsgType;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\SearchPageInput;
use App\Utils\TimServe;

class LivePlayController extends Controller
{
    protected $except = ['roomList'];

    public function roomList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyRequiredId('id');
        $page = LiveRoomService::getInstance()->pageList($input, $id);
        $list = $this->handleList(collect($page->items()));
        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        /** @var SearchPageInput $input */
        $input = SearchPageInput::new();
        $page = LiveRoomService::getInstance()->search($input);
        // 由于以TNTSearch作为驱动的scout，whereIn无法过滤数据，原因未知，此处增加filter进行数据过滤
        $list = $this->handleList(collect($page->items())->filter(function (LiveRoom $room) {
            return $room->status == 1 || $room->status == 3;
        }));
        return $this->success($this->paginate($page, $list));
    }

    private function handleList($roomList)
    {
        return $roomList->map(function (LiveRoom $room) {
            return [
                'id' => $room->id,
                'status' => $room->status,
                'title' => $room->title,
                'cover' => $room->cover,
                'shareCover' => $room->share_cover,
                'direction' => $room->direction,
                'groupId' => $room->group_id,
                'playUrl' => $room->play_url,
                'noticeTime' => $room->notice_time,
                'anchorInfo' => $room->anchorInfo
            ];
        });
    }

    public function joinRoom()
    {
        $id = $this->verifyRequiredId('id');
        $room = LiveRoomService::getInstance()->getRoom($id, [1], ['*']);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        // 增加直播间人数
        $room->views = $room->views + 1;

        // 将缓存中的实时点赞数，保存到数据库中
        $praiseNumber = LiveRoomService::getInstance()->getPraiseNumber($id);
        $room->praise_number = $praiseNumber;

        $room->save();

        // 发送即时通讯消息（用户进入直播间）
        $data = [
            'type' => LiveGroupMsgType::JOIN_ROOM,
            'data' => [
                'nickname' => $this->user()->nickname
            ]
        ];
        TimServe::new()->sendGroupSystemNotification($room->group_id, $data);

        // 获取历史聊天消息列表
        $historyChatMsgList = LiveRoomService::getInstance()->getChatMsgList($id);

        // 获取当前用户关注状态
        $fanIds = FanService::getInstance()->fanIds($room->user_id);
        $isFollow = in_array($this->userId(), $fanIds) || $this->userId() == $room->anchorInfo->id;

        // 返回
        // 实时数据：点赞数、观看人数、历史聊天消息列表、商品列表
        // 用户状态：是否关注直播间、用户粉丝等级（待开发）
        return $this->success([
            'views' => $room->views,
            'praiseNumber' => $room->praise_number,
            'hotGoods' => $room->hotGoods(),
            'historyChatMsgList' => $historyChatMsgList,
            'isFollow' => $isFollow
        ]);
    }

    public function praise()
    {
        $id = $this->verifyRequiredId('id');
        $count = $this->verifyRequiredInteger('count');

        $room = LiveRoomService::getInstance()->getRoom($id, [1]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        $praiseNumber = LiveRoomService::getInstance()->cachePraiseNumber($id, $count);

        // 发送及时通讯消息（点赞数更新）
        $data = [
            'type' => LiveGroupMsgType::PRAISE,
            'data' => [
                'praiseNumber' => $praiseNumber
            ]
        ];
        TimServe::new()->sendGroupSystemNotification($room->group_id, $data);

        return $this->success();
    }

    public function comment()
    {
        $id = $this->verifyRequiredId('id');
        $content = $this->verifyRequiredString('content');
        $identity = $this->verifyRequiredInteger('identity');

        $room = LiveRoomService::getInstance()->getRoom($id, [1]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        $chatMsg = [
            'identity' => $identity,
            'userId' => $this->userId(),
            'avatar' => $this->user()->avatar,
            'nickname' => $this->user()->nickname,
            'content' => $content
        ];
        LiveRoomService::getInstance()->cacheChatMsg($id, $chatMsg);

        return $this->success();
    }

    public function roomGoodsList()
    {
        /** @var PageInput $input */
        $input = PageInput::new();
        $id = $this->verifyRequiredId('id');

        $room = LiveRoomService::getInstance()->getRoom($id, [1], ['*'], true);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        $page = LiveGoodsService::getInstance()->pageList($id, $input, ['goods_id', 'is_hot']);
        $liveGoodsList = collect($page->items());
        $goodsIds = $liveGoodsList->pluck('goods_id')->toArray();
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds, ['id', 'name', 'cover', 'price', 'market_price', 'stock'])->keyBy('id');
        $list = $liveGoodsList->map(function (LiveGoods $liveGoods) use ($goodsList) {
            $goodsInfo = $goodsList->get($liveGoods->goods_id);
            $goodsInfo['is_hot'] = $liveGoods->is_hot;
            return $goodsInfo;
        });

        return $this->success($this->paginate($page, $list));
    }

    public function roomHotGoods()
    {
        $id = $this->verifyRequiredId('id');

        $room = LiveRoomService::getInstance()->getRoom($id, [1]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        return $this->success($room->hotGoods());
    }

    public function subscribe()
    {
        $anchorId = $this->verifyRequiredId('anchorId');
        // todo 订阅逻辑
        return $this->success();
    }
}
