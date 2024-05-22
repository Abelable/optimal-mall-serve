<?php

namespace App\Services;

use App\Models\CartGoods;
use App\Models\OrderGoods;

class OrderGoodsService extends BaseService
{
    public function createList($cartGoodsList, $orderId)
    {
        /** @var CartGoods $cartGoods */
        foreach ($cartGoodsList as $cartGoods) {
            $goods = OrderGoods::new();
            $goods->order_id = $orderId;
            $goods->goods_id = $cartGoods->goods_id;
            $goods->cover = $cartGoods->cover;
            $goods->name = $cartGoods->name;
            $goods->selected_sku_name = $cartGoods->selected_sku_name;
            $goods->selected_sku_index = $cartGoods->selected_sku_index;
            $goods->price = $cartGoods->price;
            $goods->number = $cartGoods->number;
            $goods->save();
        }
    }

    public function getListByOrderId($orderId, $columns = ['*'])
    {
        return OrderGoods::query()->where('order_id', $orderId)->get($columns);
    }

    public function getListByOrderIds(array $orderIds, $columns = ['*'])
    {
        return OrderGoods::query()->whereIn('order_id', $orderIds)->get($columns);
    }

    public function delete($orderId)
    {
        return OrderGoods::query()->where('order_id', $orderId)->delete();
    }
}
