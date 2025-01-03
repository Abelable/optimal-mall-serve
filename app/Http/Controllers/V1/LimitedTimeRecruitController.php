<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Goods;
use App\Models\NewYearGoods;
use App\Services\GoodsService;
use App\Services\LimitedTimeRecruitGoodsService;
use App\Services\LimitedTimeRecruitCategoryService;

class LimitedTimeRecruitController extends Controller
{
    protected $only = [];

    public function categoryOptions()
    {
        $list = LimitedTimeRecruitCategoryService::getInstance()->getCategoryOptions(['id', 'name']);
        return $this->success($list);
    }

    public function goodsList()
    {
        $categoryId = $this->verifyRequiredId('categoryId');
        $limitedRecruitLocalGoodsList = LimitedTimeRecruitGoodsService::getInstance()->getGoodsList($categoryId);
        $goodsIds = $limitedRecruitLocalGoodsList->pluck('goods_id')->toArray();

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');

        $list = $limitedRecruitLocalGoodsList->map(function (NewYearGoods $limitedRecruitGoods) use ($goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($limitedRecruitGoods->goods_id);
            if (is_null($goods)) {
                LimitedTimeRecruitGoodsService::getInstance()->deleteById($limitedRecruitGoods->id);
            }
            return $goods;
        })->filter(function ($goods) {
            return !is_null($goods);
        })->values();

        // todo 商品列表存缓存

        return $this->success($list);
    }
}
