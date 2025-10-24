<?php

namespace App\Utils\Inputs;

class GoodsReturnAddressInput extends BaseInput
{
    public $consigneeName;
    public $mobile;
    public $addressDetail;
    public $supplement;

    public function rules()
    {
        return [
            'consigneeName' => 'required|string',
            'mobile' => 'required|regex:/^1[3-9]\d{9}$/',
            'addressDetail' => 'required|string',
            'supplement' => 'string',
        ];
    }
}
