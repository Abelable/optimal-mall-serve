<?php

namespace App\Services;

use App\Models\OrderPackageGoods;

class OrderPackageGoodsService extends BaseService
{
    public function createOrderPackageGoods($packageId, $goodsId, $goodsCover, $goodsNumber)
    {
        $goods = OrderPackageGoods::new();
        $goods->package_id = $packageId;
        $goods->goods_id = $goodsId;
        $goods->goods_cover = $goodsCover;
        $goods->goods_number = $goodsNumber;
        $goods->save();
    }
}
