<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Goods;
use App\Services\ActivityService;
use App\Services\ActivitySubscriptionService;
use App\Services\AddressService;
use App\Services\CartGoodsService;
use App\Services\CouponService;
use App\Services\GiftGoodsService;
use App\Services\CategoryService;
use App\Services\GoodsRealImageService;
use App\Services\GoodsService;
use App\Services\MerchantService;
use App\Services\OrderGoodsService;
use App\Services\UserCouponService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\GoodsPageInput;
use App\Utils\Inputs\RecommendGoodsPageInput;

class GoodsController extends Controller
{
    protected $only = ['getPurchasedList'];

    public function categoryOptions()
    {
        $options = CategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($options);
    }

    public function list()
    {
        /** @var GoodsPageInput $input */
        $input = GoodsPageInput::new();
        $page = GoodsService::getInstance()->getGoodsPage($input);
        $list = $this->handleGoodsList($page);
        return $this->success($this->paginate($page, $list));
    }

    public function recommendList()
    {
        /** @var RecommendGoodsPageInput $input */
        $input = RecommendGoodsPageInput::new();
        $page = GoodsService::getInstance()->getRecommendGoodsList($input);
        $list = $this->handleGoodsList($page);
        return $this->success($this->paginate($page, $list));
    }

    public function search()
    {
        $keywords = $this->verifyRequiredString('keywords');
        /** @var GoodsPageInput $input */
        $input = GoodsPageInput::new();
        $page = GoodsService::getInstance()->search($keywords, $input);
        $list = $this->handleGoodsList($page);
        return $this->success($this->paginate($page, $list));
    }

    private function handleGoodsList($page)
    {
        $goodsList = collect($page->items());
        $goodsIds = $goodsList->pluck('id')->toArray();

        $activityList = ActivityService::getInstance()
            ->getActivityListByGoodsIds($goodsIds, [0, 1], ['status', 'name', 'start_time', 'end_time', 'goods_id', 'followers', 'sales'])
            ->keyBy('goods_id');

        $groupedCouponList = CouponService::getInstance()
            ->getCouponListByGoodsIds($goodsIds, ['goods_id', 'name', 'denomination', 'type', 'num_limit', 'price_limit'])
            ->groupBy('goods_id');

        $giftGoodsIds = GiftGoodsService::getInstance()->getGoodsList([1, 2])->pluck('goods_id')->toArray();

        return $goodsList->map(function (Goods $goods) use ($activityList, $groupedCouponList, $giftGoodsIds) {
            $activity = $activityList->get($goods->id);
            $goods['activityInfo'] = $activity;

            $couponList = $groupedCouponList->get($goods->id);
            $goods['couponList'] = $couponList ?: [];

            $goods['isGift'] = in_array($goods->id, $giftGoodsIds) ? 1 : 0;

            return $goods;
        });
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $addressId = $this->verifyId('addressId');

        $columns = [
            'id',
            'status',
            'merchant_id',
            'name',
            'introduction',
            'video',
            'cover',
            'price',
            'market_price',
            'freight_template_id',
            'commission_rate',
            'refund_status',
            'number_limit',
            'image_list',
            'detail_image_list',
            'stock',
            'sales_volume',
            'default_spec_image',
            'spec_list',
            'sku_list'
        ];
        $goods = GoodsService::getInstance()->getGoodsById($id, $columns);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $realImages = GoodsRealImageService::getInstance()->getByGoodsId($goods->id);
        $goods['realImageList'] = json_decode($realImages);

        $goods->image_list = json_decode($goods->image_list);
        $goods->detail_image_list = json_decode($goods->detail_image_list);
        $goods->spec_list = json_decode($goods->spec_list);
        $goods->sku_list = json_decode($goods->sku_list);

        $goods['categoryIds'] = $goods->categories->pluck('category_id')->toArray();
        unset($goods->categories);

        if ($this->isLogin()) {
            $addressColumns = ['id', 'name', 'mobile', 'region_code_list', 'region_desc', 'address_detail'];
            if (is_null($addressId)) {
                /** @var Address $address */
                $address = AddressService::getInstance()->getDefaultAddress($this->userId(), $addressColumns);
            } else {
                /** @var Address $address */
                $address = AddressService::getInstance()->getUserAddressById($this->userId(), $addressId, $addressColumns);
            }
            $goods['addressInfo'] = $address;
        }

        $activityColumns = ['id', 'status', 'name', 'start_time', 'end_time', 'goods_id', 'followers', 'sales'];
        $activity = ActivityService::getInstance()->getActivityByGoodsId($goods->id, [0, 1], $activityColumns);
        if (!is_null($activity)) {
            if ($this->isLogin()) {
                $subscription = ActivitySubscriptionService::getInstance()->getUserSubscription($this->userId(), $activity->id);
                if (!is_null($subscription)) {
                    $activity['isSubscribed'] = 1;
                }
            }
            $goods['activityInfo'] = $activity;
        }

        $couponList = CouponService::getInstance()->getCouponListByGoodsId($goods->id);
        if ($this->isLogin()) {
            $receivedCouponIds = UserCouponService::getInstance()->getUserCouponList($this->userId())->pluck('coupon_id')->toArray();
            $usedCountList = UserCouponService::getInstance()
                ->getUsedCount($this->userId())
                ->keyBy('coupon_id')
                ->map(function($item) {
                    return $item->receive_count;
                });
            $couponList = $couponList->map(function (Coupon $coupon) use ($receivedCouponIds, $usedCountList) {
                if (in_array($coupon->id, $receivedCouponIds)) {
                    $coupon['isReceived'] = 1;
                } elseif ($coupon->receive_num_limit != 0 && $usedCountList->get($coupon->id) >= $coupon->receive_num_limit) {
                    $coupon['isUsed'] = 1;
                }
                return $coupon;
            });
        }
        $goods['couponList'] = $couponList;

        $giftGoods = GiftGoodsService::getInstance()->getGoodsByGoodsId($goods->id);
        $goods['isGift'] = !is_null($giftGoods) ? 1 : 0;

        $merchant = MerchantService::getInstance()->getMerchantById($goods->merchant_id, ['id', 'name', 'mobile', 'license']);
        if (!is_null($merchant)) {
            $merchant->license = json_decode($merchant->license);
            $goods['merchantInfo'] = $merchant;
            unset($goods->merchant_id);
        }

        if ($goods->freight_template_id != 0) {
            $goods['freightTemplateInfo'] = $goods->freightTemplateInfo;
            unset($goods->freight_template_id);
        }

        return $this->success($goods);
    }

    public function getMerchantInfo()
    {
        $merchantId = $this->verifyRequiredId('merchantId');
        $merchant = MerchantService::getInstance()->getMerchantById($merchantId);
        if (is_null($merchant)) {
            return $this->fail(CodeResponse::NOT_FOUND, '商家不存在');
        }
        return $this->success($merchant);
    }

    public function getPurchasedList()
    {
        $goodsId = $this->verifyRequiredId('goodsId');
        $scene = $this->verifyRequiredInteger('scene');

        $columns = ['selected_sku_name', 'selected_sku_index', 'number'];
        $orderGoodsList = OrderGoodsService::getInstance()->getRecentlyUserListByGoodsIds($this->userId(), [$goodsId], $columns);
        $cartGoodsList = CartGoodsService::getInstance()->getListByGoodsId($this->userId(), $goodsId, $columns);
        $purchasedList = collect($orderGoodsList);
        if ($scene == 1) {
            $purchasedList = $purchasedList->merge(collect($cartGoodsList));
        }
        $list = $purchasedList->groupBy(function ($item) {
            return $item['selected_sku_name'] . '|' . $item['selected_sku_index'];
        })->map(function ($groupedItems) {
            return [
                'skuName' => $groupedItems->first()['selected_sku_name'],
                'skuIndex' => $groupedItems->first()['selected_sku_index'],
                'number' => $groupedItems->sum('number'),
            ];
        })->values()->toArray();

        return $this->success($list);
    }
}
