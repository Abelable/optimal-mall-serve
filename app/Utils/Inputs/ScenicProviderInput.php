<?php

namespace App\Utils\Inputs;

class ScenicProviderInput extends BaseInput
{
    public $companyName;
    public $regionDesc;
    public $regionCodeList;
    public $addressDetail;
    public $businessLicensePhoto;
    public $name;
    public $mobile;
    public $email;
    public $idCardNumber;
    public $idCardFrontPhoto;
    public $idCardBackPhoto;
    public $holdIdCardPhoto;
    public $bankCardOwnerName;
    public $bankCardNumber;
    public $bankName;
    public $shopName;
    public $scenicIds;

    public function rules()
    {
        return [
            'companyName' => 'required_if:type,2',
            'regionDesc' => 'required|string',
            'regionCodeList' => 'required|string',
            'addressDetail' => 'required|string',
            'businessLicensePhoto' => 'required_if:type,2',
            'name' => 'required|string',
            'mobile' => 'required|regex:/^1[345789][0-9]{9}$/',
            'email' => 'required|email',
            'idCardNumber' => 'required|string',
            'idCardFrontPhoto' => 'required|string',
            'idCardBackPhoto' => 'required|string',
            'holdIdCardPhoto' => 'required|string',
            'bankCardOwnerName' => 'required|string',
            'bankCardNumber' => 'required|string',
            'bankName' => 'required|string',
            'shopName' => 'required|string',
            'scenicIds' => 'required|string',
        ];
    }
}
