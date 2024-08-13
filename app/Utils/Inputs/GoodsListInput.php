<?php

namespace App\Utils\Inputs;

class GoodsListInput extends BaseInput
{
    public $goodsIds;

    public function rules()
    {
        return [
            'goodsIds' => 'required|array|min:1'
        ];
    }
}
