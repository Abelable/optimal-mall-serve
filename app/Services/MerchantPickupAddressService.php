<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\MerchantPickupAddress;
use App\Models\MerchantRefundAddress;
use App\Utils\Inputs\Admin\MerchantInput;
use App\Utils\Inputs\Admin\MerchantListInput;
use App\Utils\Inputs\PageInput;

class MerchantPickupAddressService extends BaseService
{
    public function createAddress($merchantId, $longitude, $latitude, $addressDetail)
    {
        $address = MerchantPickupAddress::new();
        $address->merchant_id = $merchantId;
        $address->longitude = $longitude;
        $address->latitude = $latitude;
        $address->address_detail = $addressDetail;
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

    public function getAddressOptions($merchantId, $columns = ['*'])
    {
        return MerchantPickupAddress::query()->where('merchant_id', $merchantId)->get($columns);
    }
}
