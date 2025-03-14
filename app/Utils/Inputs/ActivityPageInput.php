<?php

namespace App\Utils\Inputs;

class ActivityPageInput extends PageInput
{
    public $name;
    public $status;
    public $tag;
    public $goodsTag;
    public $goodsId;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'name' => 'string',
            'status' => 'integer|in:0,1,2',
            'tag' => 'integer|in:1,2,3,4,5',
            'goodsTag' => 'integer|in:1,2',
            'goodsId' => 'integer|digits_between:1,20',
        ]);
    }
}
