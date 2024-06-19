<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class OrderPageInput extends PageInput
{
    public $orderSn;
    public $status;
    public $consignee;
    public $mobile;

    public function rules()
    {
        return array_merge([
            'orderSn' => 'string',
            'status' => 'integer',
            'consignee' => 'string',
            'mobile' => 'regex:/^1[345789][0-9]{9}$/',
        ], parent::rules());
    }
}
