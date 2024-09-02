<?php

namespace App\Utils\Inputs;

class ActivityPageInput extends PageInput
{
    public $name;
    public $status;
    public $tag;
    public $goodsTag;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'name' => 'string',
            'status' => 'integer|in:0,1,2',
            'tag' => 'integer|in:1,2',
            'goodsTag' => 'integer|in:1,2',
        ]);
    }
}
