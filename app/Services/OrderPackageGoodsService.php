<?php

namespace App\Services;

use App\Models\OrderGoods;
use App\Models\OrderPackageGoods;

class OrderPackageGoodsService extends BaseService
{

    public function create($orderId, $packageId, $goodsId, $goodsCover, $goodsName, $goodsNumber)
    {
        $goods = OrderPackageGoods::new();
        $goods->order_id = $orderId;
        $goods->package_id = $packageId;
        $goods->goods_id = $goodsId;
        $goods->goods_cover = $goodsCover;
        $goods->goods_name = $goodsName;
        $goods->goods_number = $goodsNumber;
        $goods->save();
    }

    public function getListByOrderId($orderId, $columns = ['*'])
    {
        return OrderPackageGoods::query()->where('order_id', $orderId)->get($columns);
    }
}
