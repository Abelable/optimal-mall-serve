<?php

namespace App\Utils\Inputs;

class CreateOrderInput extends BaseInput
{
    public $deliveryMode = 1;
    public $addressId;
    public $pickupAddressId;
    public $pickupTime;
    public $pickupMobile;
    public $cartGoodsIds;
    public $couponId;
    public $useBalance;
    public $remarks;

    public function rules()
    {
        return [
            'deliveryMode' => 'integer|in:1,2',
            'addressId' => 'integer|digits_between:1,20',
            'pickupAddressId' => 'integer|digits_between:1,20',
            'pickupTime' => 'string',
            'pickupMobile' => 'regex:/^1[3-9]\d{9}$/',
            'cartGoodsIds' => 'required|array|min:1',
            'couponId' => 'integer|digits_between:1,20',
            'useBalance' => 'integer|in:0,1',
            'remarks' => 'string'
        ];
    }
}
