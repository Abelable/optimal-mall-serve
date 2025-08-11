<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\LiveRoom;
use App\Services\GoodsService;
use App\Services\Media\Live\LiveGoodsService;
use App\Services\Media\Live\LiveRoomService;
use App\Utils\CodeResponse;
use App\Utils\Enums\LiveGroupMsgType;
use App\Utils\Enums\LiveStatus;
use App\Utils\Inputs\LiveRoomInput;
use App\Utils\TencentLiveServe;
use App\Utils\TimServe;
use Illuminate\Support\Facades\DB;

class LivePushController extends Controller
{
    protected $except = ['roomList', 'roomGoodsList', 'roomHotGoods'];

    public function createRoom()
    {
        /** @var LiveRoomInput $input */
        $input = LiveRoomInput::new();

        /** @var LiveRoom $room */
        $room = DB::transaction(function () use ($input) {
            $room = LiveRoomService::getInstance()->newLiveRoom($this->userId(), $input);
            if (count($input->goodsIds) != 0) {
                foreach ($input->goodsIds as $goodsId) {
                    LiveGoodsService::getInstance()->newGoods($room->id, $goodsId);
                }
            }
            return $room;
        });

        return $this->success($room->id);
    }

    public function roomStatus()
    {
        $columns = ['status', 'direction'];
        $room = LiveRoomService::getInstance()->getUserRoom($this->userId(), [0, 1, 3], $columns);
        return $this->success($room);
    }

    public function noticeRoomInfo()
    {
        $columns = ['id', 'title', 'cover', 'share_cover', 'notice_time'];
        $room = LiveRoomService::getInstance()->getUserRoom($this->userId(), [3], $columns);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }
        return $this->success($room);
    }

    public function deleteNoticeRoom()
    {
        $room = LiveRoomService::getInstance()->getUserRoom($this->userId(), [3]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }
        $room->delete();
        return $this->success();
    }

    public function pushRoomInfo()
    {
        $columns = ['id', 'status', 'title', 'cover', 'share_cover', 'direction', 'group_id', 'push_url', 'views', 'praise_number'];

        $room = LiveRoomService::getInstance()->getUserRoom($this->userId(), [0, 1, 3], $columns);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        if (($room->status == LiveStatus::UN_START || $room->status == LiveStatus::NOTICE) && !$room->group_id) {
            // 创建群聊，获取群组id
            $groupId = TimServe::new()->createChatGroup($room->id);
            $room->group_id = $groupId;

            // 获取推、拉流地址
            $pushUrl = TencentLiveServe::new()->getPushUrl($room->id);
            $playUrl = TencentLiveServe::new()->getPlayUrl($room->id);
            $room->push_url = $pushUrl;
            $room->play_url = $playUrl;

            $room->save();
        }

        if ($room->status == LiveStatus::LIVE) {
            // 将缓存中的实时点赞数，保存到数据库中
            $praiseNumber = LiveRoomService::getInstance()->getPraiseNumber($room->id);
            $room->praise_number = $praiseNumber;
            $room->save();

            // 获取历史聊天消息列表
            $historyChatMsgList = LiveRoomService::getInstance()->getChatMsgList($room->id);
            $room['historyChatMsgList'] = $historyChatMsgList;
        }

        return $this->success($room);
    }

    public function startLive()
    {
        /** @var LiveRoom $room */
        $room = LiveRoomService::getInstance()->getUserRoom($this->userId(), [0, 3]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        $room->status = LiveStatus::LIVE;
        $room->start_time = time();
        $room->save();

        // todo 开播通知（微信模板消息）

        return $this->success();
    }

    public function stopLive()
    {
        $room = LiveRoomService::getInstance()->getUserRoom($this->userId(), [1]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        $endTime = time();
        $room->end_time = $endTime;
        $room->status = LiveStatus::STOP;

        // 保存点赞数
        $praiseNumber = LiveRoomService::getInstance()->getPraiseNumber($room->id);
        $room->praise_number = $praiseNumber;

        $timServe = TimServe::new();
        // 发送即时通讯消息（关闭直播间）
        $data = [
            'type' => LiveGroupMsgType::LIVE_END,
            'data' => [
                'liveDuration' => $endTime - $room->start_time
            ]
        ];
        $timServe->sendGroupSystemNotification($room->group_id, $data);
        // 解散聊天群组
        $timServe->destroyChatGroup($room->group_id);

        // 生成回放地址
//        $playbackUrl = TencentLiveServe::new()->liveRealTimeClip($room->id, $room->start_time, $room->end_time);
//        $room->playback_url = $playbackUrl;

        $room->save();

        // 清空缓存数据
        LiveRoomService::getInstance()->clearChatMsgList($room->id);
        LiveRoomService::getInstance()->clearPraiseNumber($room->id);

        return $this->success();
    }

    public function pushRoomGoodsList()
    {
        $status = $this->verifyRequiredInteger('status');

        $room = LiveRoomService::getInstance()->getUserRoom($this->userId(), [1]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        if ($status == 1) {
            $hotGoodsId = LiveGoodsService::getInstance()->hotGoodsId($room->id);
            $goodsList = $room->goodsList->map(function (Goods $goods) use ($hotGoodsId) {
                $goods['is_hot'] = $goods->id == $hotGoodsId;
                return $goods;
            });
        } else {
            $goodsIds = LiveGoodsService::getInstance()->goodsIds($room->id);
            $columns = ['id', 'cover', 'name', 'price', 'market_price', 'stock'];

            // todo 改为参数传入shopId
            $goodsList = GoodsService::getInstance()
                ->getLiveUnlistedGoodsList($this->user()->shop->id, $goodsIds, $columns);
        }

        return $this->success($goodsList);
    }

    public function listingGoods()
    {
        $goodsIds = $this->verifyArrayNotEmpty('goodsIds');

        $room = LiveRoomService::getInstance()->getUserRoom($this->userId(), [1]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        $listedGoodsIds = LiveGoodsService::getInstance()->goodsIds($room->id);

        foreach ($goodsIds as $goodsId) {
            if (in_array($goodsId, $listedGoodsIds)) {
                return $this->fail(CodeResponse::INVALID_OPERATION, 'id为' . $goodsId . '的商品已上架');
            }
            LiveGoodsService::getInstance()->newGoods($room->id, $goodsId);
        }

        return $this->success();
    }

    public function delistingGoods()
    {
        $goodsIds = $this->verifyArrayNotEmpty('goodsIds');

        $room = LiveRoomService::getInstance()->getUserRoom($this->userId(), [1]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        LiveGoodsService::getInstance()->deleteGoods($room->id, $goodsIds);

        return $this->success();
    }

    public function setHotGoods()
    {
        $goodsId = $this->verifyRequiredInteger('goodsId');

        $room = LiveRoomService::getInstance()->getUserRoom($this->userId(), [1]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        DB::transaction(function () use ($goodsId, $room) {
            $hotGoods = LiveGoodsService::getInstance()->hotGoods($room->id);
            if (!is_null($hotGoods) && $hotGoods->goods_id != 0 && $hotGoods->goods_id != $goodsId) {
                $hotGoods->is_hot = 0;
                $hotGoods->save();
            }

            $goods = LiveGoodsService::getInstance()->goods($room->id, $goodsId);
            $goods->is_hot = 1;
            $goods->save();
        });

        $goodsInfo = GoodsService::getInstance()->getGoodsById($goodsId, ['id', 'name', 'price', 'market_price', 'stock']);

        // 发送即时通讯消息
        $data = [
            'type' => LiveGroupMsgType::HOT_GOODS,
            'data' => [
                'hotGoods' => $goodsInfo
            ]
        ];
        TimServe::new()->sendGroupSystemNotification($room->group_id, $data);

        return $this->success();
    }

    public function cancelHotGoods()
    {
        $goodsId = $this->verifyRequiredInteger('goodsId');

        $room = LiveRoomService::getInstance()->getUserRoom($this->userId(), [1]);
        if (is_null($room)) {
            return $this->fail(CodeResponse::NOT_FOUND, '直播间不存在');
        }

        $goods = LiveGoodsService::getInstance()->goods($room->id, $goodsId);
        $goods->is_hot = 0;
        $goods->save();

        // 发送即时通讯消息
        $data = [
            'type' => LiveGroupMsgType::HOT_GOODS,
            'data' => [
                'hotGoods' => null
            ]
        ];
        TimServe::new()->sendGroupSystemNotification($room->group_id, $data);

        return $this->success();
    }
}
