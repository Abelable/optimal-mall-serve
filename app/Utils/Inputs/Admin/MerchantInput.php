<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class MerchantInput extends BaseInput
{
    public $name;
    public $companyName;
    public $consigneeName;
    public $mobile;
    public $addressDetail;
    public $managerIds;
    public $license;
    public $supplement;

    public function rules()
    {
        return [
            'name' => 'required|string',
            'companyName' => 'string',
            'consigneeName' => 'string',
            'mobile' => 'string',
            'addressDetail' => 'string',
            'managerIds' => 'array',
            'license' => 'array',
            'supplement' => 'string'
        ];
    }
}
