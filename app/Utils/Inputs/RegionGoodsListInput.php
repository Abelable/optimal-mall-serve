<?php

namespace App\Utils\Inputs;

class RegionGoodsListInput extends BaseInput
{
    public $regionId;
    public $goodsIds;

    public function rules()
    {
        return [
            'regionId' => 'integer|digits_between:1,20',
            'goodsIds' => 'required|array|min:1'
        ];
    }
}
