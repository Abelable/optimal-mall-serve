<?php

namespace App\Utils\Inputs;

class GiftGoodsListInput extends GoodsListInput
{
    public $type;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'type' => 'required|integer|in:1,2',
        ]);
    }
}
