<?php

namespace App\Services;

use App\Models\MerchantRefundAddress;
use App\Utils\Inputs\PageInput;

class MerchantRefundAddressService extends BaseService
{
    public function createAddress($merchantId, $consigneeName, $mobile, $addressDetail): MerchantRefundAddress
    {
        $address = MerchantRefundAddress::new();
        return $this->updateAddress($address, $merchantId, $consigneeName, $mobile, $addressDetail);
    }

    public function updateAddress(MerchantRefundAddress $address, $merchantId, $consigneeName, $mobile, $addressDetail): MerchantRefundAddress
    {
        $address->merchant_id = $merchantId;
        $address->consignee_name = $consigneeName;
        $address->mobile = $mobile;
        $address->address_detail = $addressDetail;
        $address->save();
        return $address;
    }

    public function deleteAddress($id)
    {
        MerchantRefundAddress::query()->where('id', $id)->delete();
    }

    public function getAddressPage($merchantId, PageInput $input, $columns = ['*'])
    {

        return MerchantRefundAddress::query()
            ->where('merchant_id', $merchantId)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getAddressById($id, $columns = ['*'])
    {
        return MerchantRefundAddress::query()->find($id, $columns);
    }

    public function getAddressOptions($merchantId, $columns = ['*'])
    {
        return MerchantRefundAddress::query()->where('merchant_id', $merchantId)->get($columns);
    }
}
