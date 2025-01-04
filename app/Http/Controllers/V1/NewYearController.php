<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\NewYearCultureGoods;
use App\Models\NewYearGoods;
use App\Models\NewYearLocalGoods;
use App\Services\GoodsService;
use App\Services\NewYearCultureGoodsService;
use App\Services\NewYearGoodsService;
use App\Services\NewYearLocalGoodsService;
use App\Services\NewYearLocalRegionService;

class NewYearController extends Controller
{
    protected $only = [];

    public function goodsList()
    {
        $newYearGoodsList = NewYearGoodsService::getInstance()->getGoodsList();
        $goodsIds = $newYearGoodsList->pluck('goods_id')->toArray();

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');

        $list = $newYearGoodsList->map(function (NewYearGoods $newYearGoods) use ($goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($newYearGoods->goods_id);
            if (is_null($goods)) {
                NewYearGoodsService::getInstance()->deleteById($newYearGoods->id);
            }
            return $goods;
        })->filter(function ($goods) {
            return !is_null($goods);
        })->values();

        // todo 商品列表存缓存

        return $this->success($list);
    }

    public function cultureGoodsList()
    {
        $newYearCultureGoodsList = NewYearCultureGoodsService::getInstance()->getGoodsList();
        $goodsIds = $newYearCultureGoodsList->pluck('goods_id')->toArray();

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');

        $list = $newYearCultureGoodsList->map(function (NewYearCultureGoods $newYearCultureGoods) use ($goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($newYearCultureGoods->goods_id);
            if (is_null($goods)) {
                NewYearCultureGoodsService::getInstance()->deleteById($newYearCultureGoods->id);
            }
            return $goods;
        })->filter(function ($goods) {
            return !is_null($goods);
        })->values();

        // todo 商品列表存缓存

        return $this->success($list);
    }

    public function regionOptions()
    {
        $list = NewYearLocalRegionService::getInstance()->getRegionOptions(['id', 'name']);
        return $this->success($list);
    }

    public function localGoodsList()
    {
        $regionId = $this->verifyRequiredId('regionId');
        $newYearLocalGoodsList = NewYearLocalGoodsService::getInstance()->getGoodsList($regionId);
        $goodsIds = $newYearLocalGoodsList->pluck('goods_id')->toArray();

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');

        $list = $newYearLocalGoodsList->map(function (NewYearLocalGoods $newYearLocalGoods) use ($goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($newYearLocalGoods->goods_id);
            if (is_null($goods)) {
                NewYearLocalGoodsService::getInstance()->deleteById($newYearLocalGoods->id);
            }
            return $goods;
        })->filter(function ($goods) {
            return !is_null($goods);
        })->values();

        // todo 商品列表存缓存

        return $this->success($list);
    }
}
