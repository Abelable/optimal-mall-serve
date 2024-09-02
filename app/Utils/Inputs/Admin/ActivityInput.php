<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class ActivityInput extends BaseInput
{
    public $status;
    public $name;
    public $startTime;
    public $endTime;
    public $goodsIds;

    public function rules()
    {
        return [
            'status' => 'required|integer|in:0,1',
            'name' => 'required|string',
            'startTime' => 'string',
            'endTime' => 'string',
            'goodsIds' => 'required|array'
        ];
    }
}
