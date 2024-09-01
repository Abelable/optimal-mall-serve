<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class CouponInput extends BaseInput
{
    public $name;
    public $denomination;
    public $description;
    public $goodsIds;
    public $numLimit;
    public $priceLimit;
    public $expirationTime;

    public function rules()
    {
        return [
            'denomination' => 'required|integer|digits_between:1,20',
            'name' => 'required|string',
            'description' => 'required|string',
            'goodsIds' => 'required|array',
            'numLimit' => 'integer|digits_between:1,20',
            'priceLimit' => 'integer|digits_between:1,20',
            'expirationTime' => 'string'
        ];
    }
}
