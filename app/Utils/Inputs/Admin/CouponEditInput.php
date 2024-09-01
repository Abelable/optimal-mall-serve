<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class CouponEditInput extends BaseInput
{
    public $id;
    public $name;
    public $denomination;
    public $description;
    public $numLimit;
    public $priceLimit;
    public $expirationTime;

    public function rules()
    {
        return [
            'id' => 'required|integer|digits_between:1,20',
            'denomination' => 'required|integer|digits_between:1,20',
            'name' => 'required|string',
            'description' => 'required|string',
            'numLimit' => 'integer|digits_between:1,20',
            'priceLimit' => 'integer|digits_between:1,20',
            'expirationTime' => 'string'
        ];
    }
}
