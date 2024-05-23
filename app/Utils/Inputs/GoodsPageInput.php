<?php

namespace App\Utils\Inputs;

class GoodsPageInput extends PageInput
{
    public $goodsIds;
    public $categoryId;
    public $sort;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'goodsIds' => 'array',
            'categoryId' => 'integer|digits_between:1,20',
            'sort' => 'string',
        ]);
    }
}
