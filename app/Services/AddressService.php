<?php

namespace App\Services;

use App\Models\Address;

class AddressService extends BaseService
{
    public function getList($userId, $columns=['*'])
    {
        return Address::query()
            ->where('user_id', $userId)
            ->orderByRaw("CASE WHEN is_default = 1 THEN 0 ELSE 1 END")
            ->get($columns);
    }

    public function getById($userId, $id, $columns=['*'])
    {
        return Address::query()->where('user_id', $userId)->find($id, $columns);
    }

    public function getDefaultAddress($userId, $columns=['*'])
    {
        $address = Address::query()->where('user_id', $userId)->where('is_default', 1)->first($columns);
        if (is_null($address)) {
            $address = Address::query()->where('user_id', $userId)->first($columns);
        }
        return $address;
    }

    public function resetDefaultAddress($userId)
    {
        $address = Address::query()->where('user_id', $userId)->where('is_default', 1)->first();
        if (!is_null($address)) {
            $address->is_default = 0;
            $address->save();
            return $address;
        }
    }
}
