<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Goods;
use App\Services\ActivityService;
use App\Services\CouponService;
use App\Services\GoodsService;
use App\Services\BannerService;
use App\Services\WxSubscriptionMessageService;
use App\Utils\CodeResponse;
use Illuminate\Support\Carbon;

class MallController extends Controller
{
    protected $only = ['subscribeActivity'];

    public function bannerList()
    {
        $list = BannerService::getInstance()->getBannerList();
        return $this->success($list);
    }

    public function activityList()
    {
        $tag = $this->verifyRequiredInteger('tag');
        $columns = ['id', 'status', 'name', 'tag', 'goods_tag', 'goods_id', 'start_time', 'end_time', 'followers', 'sales'];
        $activityList = ActivityService::getInstance()->getActivityList($tag, $columns);
        $activityKeyList = $activityList->keyBy('goods_id');

        $goodsIds = $activityList->pluck('goods_id')->toArray();
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds);

        $groupedCouponList = CouponService::getInstance()
            ->getCouponListByGoodsIds($goodsIds, ['goods_id', 'name', 'denomination', 'type', 'num_limit', 'price_limit'])
            ->groupBy('goods_id');

        $list = $goodsList->map(function (Goods $goods) use ($activityKeyList, $groupedCouponList) {
            /** @var Activity $activity */
            $activity = $activityKeyList->get($goods->id);
            $goods['activityInfo'] = $activity;

            $couponList = $groupedCouponList->get($goods->id);
            $goods['couponList'] = $couponList ?: [];

            return $goods;
        });

        // todo 缓存活动商品列表

        return $this->success($list);
    }

    public function subscribeActivity()
    {
        $activityId = $this->verifyRequiredInteger('activityId');
        $activity = ActivityService::getInstance()->getAdvanceActivityById($activityId);
        if (is_null($activity)) {
            return $this->fail(CodeResponse::NOT_FOUND, '活动预告不存在');
        }

        $templateId = env('ADVANCE_ACTIVITY_TEMPLATE_ID');
        $page = '/pages/home/subpages/goods-detail/index?id=' . $activity->goods_id;
        $openid = $this->user()->openid;
        $endTime = Carbon::parse($activity->end_time)->format('Y-m-d H:i:s');
        $data = "{'thing7': {'value': '{$activity->name}'}, 'thing8': {'value': '{$activity->goods_name}'}, 'date5': {'value': '{$endTime}'}}";
        WxSubscriptionMessageService::getInstance()->create($templateId, $page, $openid, $data);

        return $this->success();
    }
}
