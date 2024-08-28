<?php

namespace App\Utils\Inputs;

class ActivityPageInput extends PageInput
{
    public $name;
    public $status;
    public $goodsType;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'name' => 'string',
            'status' => 'integer|in:0,1,2',
            'goodsType' => 'integer|in:1,2',
        ]);
    }
}
