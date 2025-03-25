<?php

namespace App\Services;

use App\Models\MerchantPickupAddress;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\PickupAddressInput;

class MerchantPickupAddressService extends BaseService
{
    public function createAddress(PickupAddressInput $input)
    {
        $address = MerchantPickupAddress::new();
        return $this->updateAddress($address, $input);
    }

    public function updateAddress(MerchantPickupAddress $address, PickupAddressInput $input)
    {
        $address->name = $input->name;
        $address->time_frame = $input->timeFrame;
        $address->merchant_id = $input->merchantId;
        $address->longitude = $input->longitude;
        $address->latitude = $input->latitude;
        $address->address_detail = $input->addressDetail;
        $address->save();
        return $address;
    }

    public function deleteAddress($id)
    {
        MerchantPickupAddress::query()->where('id', $id)->delete();
    }

    public function getAddressPage($merchantId, PageInput $input, $columns = ['*'])
    {

        return MerchantPickupAddress::query()
            ->where('merchant_id', $merchantId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getAddressById($id, $columns = ['*'])
    {
        return MerchantPickupAddress::query()->find($id, $columns);
    }

    public function getAddressOptions($merchantId, $columns = ['*'])
    {
        return MerchantPickupAddress::query()->where('merchant_id', $merchantId)->get($columns);
    }

    public function getListByIds(array $ids, $columns = ['*'])
    {
        return MerchantPickupAddress::query()->whereIn('id', $ids)->get($columns);
    }
}
