<?php

namespace App\Utils\Inputs;

class CategoryGoodsListInput extends BaseInput
{
    public $categoryId;
    public $goodsIds;

    public function rules()
    {
        return [
            'categoryId' => 'integer|digits_between:1,20',
            'goodsIds' => 'required|array|min:1'
        ];
    }
}
