<?php

namespace App\Services;

use App\Models\GoodsPickupAddress;

class GoodsPickupAddressService extends BaseService
{
    public function createList($goodsId, array $pickupAddressIds)
    {
        $existingPickupAddressIds = $this->getListByGoodsId($goodsId)->pluck('pickup_address_id')->toArray();
        $pickupAddressIdsToDelete = array_diff($existingPickupAddressIds, $pickupAddressIds);
        $pickupAddressIdsToAdd = array_diff($pickupAddressIds, $existingPickupAddressIds);

        if (!empty($pickupAddressIdsToDelete)) {
            $this->deleteList($goodsId, $pickupAddressIdsToDelete);
        }

        if (!empty($pickupAddressIdsToAdd)) {
            $insertData = [];
            foreach ($pickupAddressIdsToAdd as $pickupAddressId) {
                $insertData[] = [
                    'goods_id' => $goodsId,
                    'pickup_address_id' => $pickupAddressId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            GoodsPickupAddress::query()->insert($insertData);
        }
    }

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

    public function getListByGoodsId($goodsId, $columns = ['*'])
    {
        return GoodsPickupAddress::query()->where('goods_id', $goodsId)->get($columns);
    }

    public function deleteList($goodsId, array $pickupAddressIds)
    {
        GoodsPickupAddress::query()
            ->where('goods_id', $goodsId)
            ->whereIn('pickup_address_id', $pickupAddressIds)
            ->delete();
    }

    public function deleteByGoodsId($goodsId)
    {
        GoodsPickupAddress::query()->where('goods_id', $goodsId)->delete();
    }
}
