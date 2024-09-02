<?php

namespace App\Utils\Inputs;

class CouponPageInput extends PageInput
{
    public $name;
    public $status;
    public $type;
    public $goodsId;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'name' => 'string',
            'status' => 'integer|in:1,2,3',
            'type' => 'integer|in:1,2,3',
            'goodsId' => 'integer|digits_between:1,20',
        ]);
    }
}
