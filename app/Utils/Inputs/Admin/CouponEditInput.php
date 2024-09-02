<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class CouponEditInput extends BaseInput
{
    public $id;
    public $name;
    public $denomination;
    public $description;
    public $type;
    public $numLimit;
    public $priceLimit;
    public $expirationTime;

    public function rules()
    {
        return [
            'id' => 'required|integer|digits_between:1,20',
            'denomination' => 'required|numeric',
            'name' => 'required|string',
            'description' => 'required|string',
            'type' => 'required|integer|in:1,2,3',
            'numLimit' => 'integer|digits_between:1,20',
            'priceLimit' => 'numeric',
            'expirationTime' => 'string'
        ];
    }
}
