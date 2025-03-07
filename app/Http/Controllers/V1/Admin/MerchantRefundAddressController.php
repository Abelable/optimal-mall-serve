<?php

namespace App\Http\Controllers\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\MerchantRefundAddressService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class MerchantRefundAddressController extends Controller
{
    protected $guard = 'Admin';

    public function list()
    {
        $merchantId = $this->verifyRequiredInteger('merchantId');
        /** @var PageInput $input */
        $input = PageInput::new();
        $page = MerchantRefundAddressService::getInstance()->getAddressPage($merchantId, $input);
        return $this->successPaginate($page);
    }

    public function detail()
    {
        $id = $this->verifyRequiredId('id');
        $address = MerchantRefundAddressService::getInstance()->getAddressById($id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前退货地址不存在');
        }
        return $this->success($address);
    }

    public function add()
    {
        $merchantId = $this->verifyRequiredInteger('merchantId');
        $consigneeName = $this->verifyRequiredString('consigneeName');
        $mobile = $this->verifyRequiredString('mobile');
        $addressDetail = $this->verifyRequiredString('addressDetail');

        $address = MerchantRefundAddressService::getInstance()->createAddress($merchantId, $consigneeName, $mobile, $addressDetail);

        return $this->success($address);
    }

    public function edit()
    {
        $id = $this->verifyRequiredId('id');
        $merchantId = $this->verifyRequiredInteger('merchantId');
        $consigneeName = $this->verifyRequiredString('consigneeName');
        $mobile = $this->verifyRequiredString('mobile');
        $addressDetail = $this->verifyRequiredString('addressDetail');

        $address = MerchantRefundAddressService::getInstance()->getAddressById($id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前退货地址不存在');
        }

        MerchantRefundAddressService::getInstance()->updateAddress($address, $merchantId, $consigneeName, $mobile, $addressDetail);

        return $this->success();
    }

    public function delete()
    {
        $id = $this->verifyRequiredId('id');
        $address = MerchantRefundAddressService::getInstance()->getAddressById($id);
        if (is_null($address)) {
            return $this->fail(CodeResponse::NOT_FOUND, '当前退货地址不存在');
        }
        $address->delete();
        return $this->success();
    }
}
