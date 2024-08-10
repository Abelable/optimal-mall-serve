<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\GoodsService;
use App\Services\RuralBannerService;
use App\Services\RuralGoodsService;
use App\Services\RuralRegionService;

class RuralController extends Controller
{
    protected $except = ['bannerList', 'list'];

    public function bannerList()
    {
        $list = RuralBannerService::getInstance()->getBannerList();
        return $this->success($list);
    }

    public function regionOptions()
    {
        $list = RuralRegionService::getInstance()->getRegionOptions(['id', 'name']);
        return $this->success($list);
    }

    public function goodsList()
    {
        $regionId = $this->verifyRequiredId('regionId');
        $goodsIds = RuralGoodsService::getInstance()->getGoodsList($regionId, ['goods_id'])->pluck('goods_id')->toArray();
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds);

        // todo 商品列表存缓存

        return $this->success($goodsList);
    }
}
