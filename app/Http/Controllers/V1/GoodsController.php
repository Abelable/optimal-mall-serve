<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Services\ActivityService;
use App\Services\CouponService;
use App\Services\GiftGoodsService;
use App\Services\CategoryService;
use App\Services\GoodsService;
use App\Services\MerchantService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\GoodsPageInput;
use App\Utils\Inputs\RecommendGoodsPageInput;

class GoodsController extends Controller
{
    protected $only = [];

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
            ->getActivityListByGoodsIds($goodsIds, ['status', 'name', 'start_time', 'end_time', 'goods_id', 'followers', 'sales'])
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
        $columns = [
            'id',
            'merchant_id',
            'video',
            'cover',
            'image_list',
            'default_spec_image',
            'name',
            'price',
            'market_price',
            'stock',
            'sales_volume',
            'detail_image_list',
            'spec_list',
            'sku_list'
        ];
        $goods = GoodsService::getInstance()->getGoodsById($id, $columns);
        if (is_null($goods)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $goods->image_list = json_decode($goods->image_list);
        $goods->detail_image_list = json_decode($goods->detail_image_list);
        $goods->spec_list = json_decode($goods->spec_list);
        $goods->sku_list = json_decode($goods->sku_list);

        $activityColumns = ['status', 'name', 'start_time', 'end_time', 'goods_id', 'followers', 'sales'];
        $activity = ActivityService::getInstance()->getActivityByGoodsId($goods->id, $activityColumns);
        $goods['activityInfo'] = $activity;

        $couponColumns = ['goods_id', 'name', 'denomination', 'description', 'type', 'num_limit', 'price_limit', 'received_num'];
        $couponList = CouponService::getInstance()->getCouponListByGoodsId($goods->id, $couponColumns);
        $goods['couponList'] = $couponList;

        $giftGoods = GiftGoodsService::getInstance()->getGoodsByGoodsId($goods->id);
        $goods['isGift'] = !is_null($giftGoods) ? 1 : 0;

        $merchant = MerchantService::getInstance()->getMerchantById($goods->merchant_id);
        $merchant->license = json_decode($merchant->license);
        $goods['merchantInfo'] = $merchant;
        unset($goods->merchant_id);

        return $this->success($goods);
    }
}
