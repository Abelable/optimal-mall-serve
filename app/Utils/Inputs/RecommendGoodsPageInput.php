<?php

namespace App\Utils\Inputs;

class RecommendGoodsPageInput extends PageInput
{
    public $goodsIds;
    public $categoryIds;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'goodsIds' => 'array',
            'categoryIds' => 'array',
        ]);
    }
}
