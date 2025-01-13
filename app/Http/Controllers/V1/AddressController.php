<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Services\AddressService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\AddressInput;

class AddressController extends Controller
{
    public function defaultAddress()
    {
        $columns = ['id', 'region_desc', 'address_detail'];
        $address = AddressService::getInstance()->getDefaultAddress($this->userId(), $columns);
        return $this->success($address);
    }

    public function list()
    {
        $columns = ['id', 'is_default', 'name', 'mobile', 'region_desc', 'address_detail'];
        $list = AddressService::getInstance()->getList($this->userId(), $columns);
        return $this->success($list);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $columns = ['id', 'is_default', 'name', 'mobile', 'region_code_list', 'region_desc', 'address_detail'];
        $address = AddressService::getInstance()->getUserAddressById($this->userId(), $id, $columns);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '收货地址不存在');
        }
        $address->region_code_list = json_decode($address->region_code_list);
        return $this->success($address);
    }

    public function add()
    {
        /** @var AddressInput $input */
        $input = AddressInput::new();

        $address = Address::new();
        $address->user_id = $this->userId();
        $this->updateAddress($address, $input);

        return $this->success();
    }

    public function edit()
    {
        /** @var AddressInput $input */
        $input = AddressInput::new();

        $address = AddressService::getInstance()->getUserAddressById($this->userId(), $input->id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '收货地址不存在');
        }
        $this->updateAddress($address, $input);

        return $this->success();
    }

    private function updateAddress(Address $address, AddressInput $input)
    {
        $address->name = $input->name;
        $address->mobile = $input->mobile;
        $address->region_desc = $input->regionDesc;
        $address->region_code_list = $input->regionCodeList;
        $address->address_detail = $input->addressDetail;
        if ($input->isDefault == 1 && $address->is_default == 0) {
            AddressService::getInstance()->resetDefaultAddress($this->userId());
        }
        $address->is_default = $input->isDefault;
        $address->save();
        return $address;
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $address = AddressService::getInstance()->getUserAddressById($this->userId(), $id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '收货地址不存在');
        }
        $address->delete();
        return $this->success();
    }
}
