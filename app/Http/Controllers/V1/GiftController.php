<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\GoodsService;
use App\Services\GiftGoodsService;

class GiftController extends Controller
{
    protected $only = [];

    public function goodsList()
    {
        $type = $this->verifyRequiredInteger('type');
        $goodsIds = GiftGoodsService::getInstance()->getGoodsList([$type])->pluck('goods_id')->toArray();
        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds);
        $list = $goodsList->map(function ($goods) {
            $goods['isGift'] = 1;
            return $goods;
        });

        // todo 商品列表存缓存

        return $this->success($list);
    }
}
