<?php

namespace App\Utils\Inputs;

class GiftGoodsPageInput extends PageInput
{
    public $type;

    public function rules()
    {
        return array_merge([
            'type' => 'required|integer|in:1,2',
        ], parent::rules());
    }
}
