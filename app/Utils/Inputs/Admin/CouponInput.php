<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class CouponInput extends BaseInput
{
    public $name;
    public $denomination;
    public $description;
    public $goodsIds;
    public $type;
    public $numLimit;
    public $priceLimit;
    public $expirationTime;
    public $receiveNumLimit;

    public function rules()
    {
        return [
            'name' => 'required|string',
            'denomination' => 'required|numeric',
            'description' => 'required|string',
            'goodsIds' => 'required|array',
            'type' => 'required|integer|in:1,2,3',
            'numLimit' => 'integer|digits_between:1,20',
            'priceLimit' => 'numeric',
            'expirationTime' => 'string',
            'receiveNumLimit' => 'integer|digits_between:1,20',
        ];
    }
}
