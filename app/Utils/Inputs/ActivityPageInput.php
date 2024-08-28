<?php

namespace App\Utils\Inputs;

class ActivityPageInput extends PageInput
{
    public $status;
    public $goodsType;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'status' => 'integer|in:0,1,2',
            'goodsType' => 'integer|in:1,2',
        ]);
    }
}
