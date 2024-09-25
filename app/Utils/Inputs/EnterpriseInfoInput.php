<?php

namespace App\Utils\Inputs;

class EnterpriseInfoInput extends BaseInput
{
    public $name;
    public $bankName;
    public $bankCardCode;
    public $bankAdress;
    public $businessLicensePhoto;
    public $idCardFrontPhoto;
    public $idCardBackPhoto;

    public function rules()
    {
        return [
            'name' => 'required|string',
            'bankName' => 'required|string',
            'bankCardCode' => 'required|string',
            'bankAddress' => 'required|string',
            'businessLicensePhoto' => 'required|string',
            'idCardFrontPhoto' => 'required|string',
            'idCardBackPhoto' => 'required|string',
        ];
    }
}
