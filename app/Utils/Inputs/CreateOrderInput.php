<?php

namespace App\Utils\Inputs;

class CreateOrderInput extends BaseInput
{
    public $addressId;
    public $cartGoodsIds;
    public $couponId;
    public $remarks;

    public function rules()
    {
        return [
            'addressId' => 'required|integer|digits_between:1,20',
            'cartGoodsIds' => 'required|array|min:1',
            'couponId' => 'integer|digits_between:1,20',
            'remarks' => 'string'
        ];
    }
}
