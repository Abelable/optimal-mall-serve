<?php

namespace App\Services;

use App\Models\GoodsPickupAddress;

class GoodsPickupAddressService extends BaseService
{
    public function createAddress($goodsId, $pickupAddressId)
    {
        $address = GoodsPickupAddress::new();
        $address->goods_id = $goodsId;
        $address->pickup_address_id = $pickupAddressId;
        $address->save();
        return $address;
    }

    public function getAddressList($goodsId, $columns = ['*'])
    {
        return GoodsPickupAddress::query()->where('goods_id', $goodsId)->get($columns);
    }
}
