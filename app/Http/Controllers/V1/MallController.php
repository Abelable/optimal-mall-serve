<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Goods;
use App\Services\ActivityService;
use App\Services\ActivitySubscriptionService;
use App\Services\CouponService;
use App\Services\GoodsService;
use App\Services\BannerService;
use App\Utils\CodeResponse;
use Illuminate\Support\Facades\DB;

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
        $columns = ['id', 'status', 'name', 'tag', 'goods_tag', 'goods_id', 'start_time', 'end_time', 'followers', 'sales', 'sort'];
        $activityList = ActivityService::getInstance()->getActivityList($tag, $columns);

        $subscribedActivityIds = [];
        if ($this->isLogin()) {
            $subscribedActivityIds = ActivitySubscriptionService::getInstance()->getUserList($this->userId())->pluck('activity_id')->toArray();
        }

        $goodsIds = $activityList->pluck('goods_id')->toArray();
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');

        $groupedCouponList = CouponService::getInstance()
            ->getCouponListByGoodsIds($goodsIds, ['goods_id', 'name', 'denomination', 'type', 'num_limit', 'price_limit'])
            ->groupBy('goods_id');

        $list = $activityList->map(function (Activity $activity) use ($groupedCouponList, $subscribedActivityIds, $goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($activity->goods_id);
            unset($activity->goods_id);

            $activity['isSubscribed'] = in_array($activity->id, $subscribedActivityIds) ? 1 : 0;
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

        DB::transaction(function () use ($activity) {
            $activity->followers = $activity->followers + 1;
            $activity->save();

            ActivitySubscriptionService::getInstance()->create($this->userId(), $this->user()->openid, $activity->id);
        });

        return $this->success();
    }
}
