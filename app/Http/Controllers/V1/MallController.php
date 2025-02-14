<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Goods;
use App\Models\VillageFreshGoods;
use App\Models\VillageGiftGoods;
use App\Models\VillageGrainGoods;
use App\Models\VillageSnackGoods;
use App\Services\ActivityService;
use App\Services\ActivitySubscriptionService;
use App\Services\ActivityTagService;
use App\Services\CouponService;
use App\Services\GiftGoodsService;
use App\Services\GoodsService;
use App\Services\OrderGoodsService;
use App\Services\VillageFreshGoodsService;
use App\Services\VillageGiftGoodsService;
use App\Services\VillageGrainGoodsService;
use App\Services\VillageSnackGoodsService;
use App\Utils\CodeResponse;
use Illuminate\Support\Facades\DB;

class MallController extends Controller
{
    protected $only = ['subscribeActivity'];

    public function activityTagOptions()
    {
        $options = ActivityTagService::getInstance()->getTagOptions(['id', 'name']);
        return $this->success($options);
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
            // todo 预告到期未开始
            if ($activity->status == 0 && strtotime($activity->start_time) <= time()) {
                ActivityService::getInstance()->startActivity($activity->id);
            }

            // todo 活动到期未结束
            if ($activity->status == 1 && strtotime($activity->end_time) <= time()) {
                $activity->status = 2;
                $activity->save();
                return null;
            }

            /** @var Goods $goods */
            $goods = $goodsList->get($activity->goods_id);
            unset($activity->goods_id);

            $activity['isSubscribed'] = in_array($activity->id, $subscribedActivityIds) ? 1 : 0;
            $goods['activityInfo'] = $activity;

            $couponList = $groupedCouponList->get($goods->id);
            $goods['couponList'] = $couponList ?: [];

            if ($activity->status == 1) {
                $goods['customerList'] = OrderGoodsService::getInstance()->getLatestCustomerList($goods->id);
            }

            return $goods;
        })->filter(function ($goods) {
            return !is_null($goods);
        })->values();

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

    public function grainGoodsList()
    {
        $grainGoodsList = VillageGrainGoodsService::getInstance()->getGoodsList();
        $goodsIds = $grainGoodsList->pluck('goods_id')->toArray();

        $activityList = ActivityService::getInstance()
            ->getActivityListByGoodsIds($goodsIds, [0, 1], ['status', 'name', 'start_time', 'end_time', 'goods_id', 'followers', 'sales'])
            ->keyBy('goods_id');

        $groupedCouponList = CouponService::getInstance()
            ->getCouponListByGoodsIds($goodsIds, ['goods_id', 'name', 'denomination', 'type', 'num_limit', 'price_limit'])
            ->groupBy('goods_id');

        $giftGoodsIds = GiftGoodsService::getInstance()->getGoodsList([1, 2])->pluck('goods_id')->toArray();

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');

        $list = $grainGoodsList->map(function (VillageGrainGoods $grainGoods) use ($giftGoodsIds, $activityList, $groupedCouponList, $goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($grainGoods->goods_id);

            if (!is_null($goods)) {
                $activity = $activityList->get($goods->id);
                $goods['activityInfo'] = $activity;

                $couponList = $groupedCouponList->get($goods->id);
                $goods['couponList'] = $couponList ?: [];

                $goods['isGift'] = in_array($goods->id, $giftGoodsIds) ? 1 : 0;
            } else {
                VillageGrainGoodsService::getInstance()->deleteById($grainGoods->id);
            }

            return $goods;
        })->filter(function ($goods) {
            return !is_null($goods);
        })->values();

        // todo 商品列表存缓存

        return $this->success($list);
    }

    public function freshGoodsList()
    {
        $freshGoodsList = VillageFreshGoodsService::getInstance()->getGoodsList();
        $goodsIds = $freshGoodsList->pluck('goods_id')->toArray();

        $activityList = ActivityService::getInstance()
            ->getActivityListByGoodsIds($goodsIds, [0, 1], ['status', 'name', 'start_time', 'end_time', 'goods_id', 'followers', 'sales'])
            ->keyBy('goods_id');

        $groupedCouponList = CouponService::getInstance()
            ->getCouponListByGoodsIds($goodsIds, ['goods_id', 'name', 'denomination', 'type', 'num_limit', 'price_limit'])
            ->groupBy('goods_id');

        $giftGoodsIds = GiftGoodsService::getInstance()->getGoodsList([1, 2])->pluck('goods_id')->toArray();

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');

        $list = $freshGoodsList->map(function (VillageFreshGoods $freshGoods) use ($giftGoodsIds, $activityList, $groupedCouponList, $goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($freshGoods->goods_id);

            if (!is_null($goods)) {
                $activity = $activityList->get($goods->id);
                $goods['activityInfo'] = $activity;

                $couponList = $groupedCouponList->get($goods->id);
                $goods['couponList'] = $couponList ?: [];

                $goods['isGift'] = in_array($goods->id, $giftGoodsIds) ? 1 : 0;
            } else {
                VillageFreshGoodsService::getInstance()->deleteById($freshGoods->id);
            }

            return $goods;
        })->filter(function ($goods) {
            return !is_null($goods);
        })->values();

        // todo 商品列表存缓存

        return $this->success($list);
    }

    public function snackGoodsList()
    {
        $snackGoodsList = VillageSnackGoodsService::getInstance()->getGoodsList();
        $goodsIds = $snackGoodsList->pluck('goods_id')->toArray();

        $activityList = ActivityService::getInstance()
            ->getActivityListByGoodsIds($goodsIds, [0, 1], ['status', 'name', 'start_time', 'end_time', 'goods_id', 'followers', 'sales'])
            ->keyBy('goods_id');

        $groupedCouponList = CouponService::getInstance()
            ->getCouponListByGoodsIds($goodsIds, ['goods_id', 'name', 'denomination', 'type', 'num_limit', 'price_limit'])
            ->groupBy('goods_id');

        $giftGoodsIds = GiftGoodsService::getInstance()->getGoodsList([1, 2])->pluck('goods_id')->toArray();

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');

        $list = $snackGoodsList->map(function (VillageSnackGoods $snackGoods) use ($giftGoodsIds, $activityList, $groupedCouponList, $goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($snackGoods->goods_id);

            if (!is_null($goods)) {
                $activity = $activityList->get($goods->id);
                $goods['activityInfo'] = $activity;

                $couponList = $groupedCouponList->get($goods->id);
                $goods['couponList'] = $couponList ?: [];

                $goods['isGift'] = in_array($goods->id, $giftGoodsIds) ? 1 : 0;
            } else {
                VillageSnackGoodsService::getInstance()->deleteById($snackGoods->id);
            }

            return $goods;
        })->filter(function ($goods) {
            return !is_null($goods);
        })->values();

        // todo 商品列表存缓存

        return $this->success($list);
    }

    public function giftGoodsList()
    {
        $giftGoodsList = VillageGiftGoodsService::getInstance()->getGoodsList();
        $goodsIds = $giftGoodsList->pluck('goods_id')->toArray();

        $activityList = ActivityService::getInstance()
            ->getActivityListByGoodsIds($goodsIds, [0, 1], ['status', 'name', 'start_time', 'end_time', 'goods_id', 'followers', 'sales'])
            ->keyBy('goods_id');

        $groupedCouponList = CouponService::getInstance()
            ->getCouponListByGoodsIds($goodsIds, ['goods_id', 'name', 'denomination', 'type', 'num_limit', 'price_limit'])
            ->groupBy('goods_id');

        $giftGoodsIds = GiftGoodsService::getInstance()->getGoodsList([1, 2])->pluck('goods_id')->toArray();

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');

        $list = $giftGoodsList->map(function (VillageGiftGoods $villageGiftGoods) use ($giftGoodsIds, $activityList, $groupedCouponList, $goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($villageGiftGoods->goods_id);

            if (!is_null($goods)) {
                $activity = $activityList->get($goods->id);
                $goods['activityInfo'] = $activity;

                $couponList = $groupedCouponList->get($goods->id);
                $goods['couponList'] = $couponList ?: [];

                $goods['isGift'] = in_array($goods->id, $giftGoodsIds) ? 1 : 0;
            } else {
                VillageGiftGoodsService::getInstance()->deleteById($villageGiftGoods->id);
            }

            return $goods;
        })->filter(function ($goods) {
            return !is_null($goods);
        })->values();

        // todo 商品列表存缓存

        return $this->success($list);
    }
}
