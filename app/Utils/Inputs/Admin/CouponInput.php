<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class CouponInput extends BaseInput
{
    public $status;
    public $name;
    public $startTime;
    public $endTime;
    public $goodsType;
    public $goodsIds;

    public function rules()
    {
        return [
            'status' => 'required|integer|in:0,1',
            'name' => 'required|string',
            'startTime' => 'string',
            'endTime' => 'string',
            'goodsType' => 'required|integer|in:1,2',
            'goodsIds' => 'required|array'
        ];
    }
}
