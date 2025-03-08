<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\MerchantPickupAddressService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;
use App\Utils\Inputs\PickupAddressInput;

class MerchantPickupAddressController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        $merchantId = $this->verifyRequiredInteger('merchantId');
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = MerchantPickupAddressService::getInstance()->getAddressPage($merchantId, $input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $address = MerchantPickupAddressService::getInstance()->getAddressById($id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前提货地址不存在');
        }
        return $this->success($address);
    }

    public function add()
    {
        /** @var PickupAddressInput $input */
        $input = PickupAddressInput::new();
        MerchantPickupAddressService::getInstance()->createAddress($input);
        return $this->success();
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        /** @var PickupAddressInput $input */
        $input = PickupAddressInput::new();

        $address = MerchantPickupAddressService::getInstance()->getAddressById($id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前提货地址不存在');
        }

        MerchantPickupAddressService::getInstance()->updateAddress($address, $input);

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $address = MerchantPickupAddressService::getInstance()->getAddressById($id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前提货地址不存在');
        }
        $address->delete();
        return $this->success();
    }
}
