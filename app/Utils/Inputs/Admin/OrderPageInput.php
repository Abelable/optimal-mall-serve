<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class OrderPageInput extends PageInput
{
    public $orderSn;
    public $status;
    public $goodsId;
    public $merchantId;
    public $userId;
    public $consignee;
    public $mobile;

    public function rules()
    {
        return array_merge([
            'orderSn' => 'string',
            'status' => 'integer',
            'goodsId' => 'integer',
            'merchantId' => 'integer',
            'userId' => 'integer',
            'consignee' => 'string',
            'mobile' => 'regex:/^1[3-9]\d{9}$/',
        ], parent::rules());
    }
}
