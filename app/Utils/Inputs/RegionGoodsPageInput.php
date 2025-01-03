<?php

namespace App\Utils\Inputs;

class RegionGoodsPageInput extends PageInput
{
    public $regionId;

    public function rules()
    {
        return array_merge([
            'regionId' => 'integer',
        ], parent::rules());
    }
}
