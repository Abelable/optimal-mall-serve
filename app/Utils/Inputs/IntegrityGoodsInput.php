<?php

namespace App\Utils\Inputs;

class IntegrityGoodsInput extends BaseInput
{
    public $goodsIds;

    public function rules()
    {
        return [
            'goodsIds' => 'required|array|min:1'
        ];
    }
}
