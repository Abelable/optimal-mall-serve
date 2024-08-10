<?php

namespace App\Utils\Inputs;

class RuralGoodsPageInput extends PageInput
{
    public $regionId;

    public function rules()
    {
        return array_merge([
            'regionId' => 'integer',
        ], parent::rules());
    }
}
