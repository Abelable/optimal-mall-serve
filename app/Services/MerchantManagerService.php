<?php

namespace App\Services;

use App\Models\MerchantManager;

class MerchantManagerService extends BaseService
{
    public function createManager($merchantId, $userId)
    {
        $address = MerchantManager::new();
        $address->merchant_id = $merchantId;
        $address->user_id = $userId;
        $address->save();
        return $address;
    }

    public function getManagerList($merchantId, $columns = ['*'])
    {
        return MerchantManager::query()->where('merchant_id', $merchantId)->get($columns);
    }

    public function deleteManager($merchantId)
    {
        MerchantManager::query()->where('merchant_id', $merchantId)->delete();
    }
}
