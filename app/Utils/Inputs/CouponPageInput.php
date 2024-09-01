<?php

namespace App\Utils\Inputs;

class CouponPageInput extends PageInput
{
    public $name;
    public $status;
    public $goodsId;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'name' => 'string',
            'status' => 'integer|in:0,1,2',
            'goodsId' => 'integer|in:1,2',
        ]);
    }
}
