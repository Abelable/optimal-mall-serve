<?php

namespace App\Services;

use App\Models\GoodsRefundAddress;

class GoodsRefundAddressService extends BaseService
{
    public function createList($goodsId, array $refundAddressIds)
    {
        $existingRefundAddressIds = $this->getListByGoodsId($goodsId)->pluck('refund_address_id')->toArray();
        $refundAddressIdsToDelete = array_diff($existingRefundAddressIds, $refundAddressIds);
        $refundAddressIdsToAdd = array_diff($refundAddressIds, $existingRefundAddressIds);

        if (!empty($refundAddressIdsToDelete)) {
            $this->deleteList($goodsId, $refundAddressIdsToDelete);
        }

        if (!empty($refundAddressIdsToAdd)) {
            $insertData = [];
            foreach ($refundAddressIdsToAdd as $refundAddressId) {
                $insertData[] = [
                    'goods_id' => $goodsId,
                    'refund_address_id' => $refundAddressId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            GoodsRefundAddress::query()->insert($insertData);
        }
    }

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

    public function getListByGoodsId($goodsId, $columns = ['*'])
    {
        return GoodsRefundAddress::query()->where('goods_id', $goodsId)->get($columns);
    }

    public function deleteList($goodsId, array $refundAddressIds)
    {
        GoodsRefundAddress::query()
            ->where('goods_id', $goodsId)
            ->whereIn('refund_address_id', $refundAddressIds)
            ->delete();
    }

    public function deleteByGoodsId($goodsId)
    {
        GoodsRefundAddress::query()->where('goods_id', $goodsId)->delete();
    }
}
