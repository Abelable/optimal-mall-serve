<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class MerchantInput extends BaseInput
{
    public $name;
    public $consigneeName;
    public $mobile;
    public $addressDetail;
    public $license;
    public $supplement;

    public function rules()
    {
        return [
            'name' => 'required|string',
            'consigneeName' => 'required|string',
            'mobile' => 'required|regex:/^1[345789][0-9]{9}$/',
            'addressDetail' => 'required|string',
            'license' => 'array',
            'supplement' => 'string'
        ];
    }
}
