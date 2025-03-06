<?php

namespace App\Services;

use App\Models\GoodsRefundAddress;

class GoodsRefundAddressService extends BaseService
{
    public function createAddress($goodsId, $refundAddressId)
    {
        $address = GoodsRefundAddress::new();
        $address->goods_id = $goodsId;
        $address->refund_address_id = $refundAddressId;
        $address->save();
        return $address;
    }

    public function getAddressList($goodsId, $columns = ['*'])
    {
        return GoodsRefundAddress::query()->where('goods_id', $goodsId)->get($columns);
    }
}
