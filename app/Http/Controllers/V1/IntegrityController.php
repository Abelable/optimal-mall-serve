<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\GoodsService;
use App\Services\IntegrityBannerService;
use App\Services\IntegrityGoodsService;

class IntegrityController extends Controller
{
    protected $only = [];

    public function bannerList()
    {
        $list = IntegrityBannerService::getInstance()->getBannerList();
        return $this->success($list);
    }

    public function goodsList()
    {
        $goodsIds = IntegrityGoodsService::getInstance()->getGoodsList(['goods_id'])->pluck('goods_id')->toArray();
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds);

        // todo 商品列表存缓存

        return $this->success($goodsList);
    }
}
