<?php

namespace App\Utils\Inputs;

class RuralGoodsInput extends BaseInput
{
    public $regionId;
    public $goodsIds;

    public function rules()
    {
        return [
            'regionId' => 'required|integer|digits_between:1,20',
            'goodsIds' => 'required|array|min:1'
        ];
    }
}
