<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class GoodsCategoryInput extends BaseInput
{
    public $shopCategoryId;
    public $name;
    public $minLeaderCommissionRate;
    public $maxLeaderCommissionRate;
    public $minShareCommissionRate;
    public $maxShareCommissionRate;

    public function rules()
    {
        return [
            'shopCategoryId' => 'required|integer|digits_between:1,20',
            'name' => 'required|string',
            'minLeaderCommissionRate' => 'required|integer',
            'maxLeaderCommissionRate' => 'required|integer',
            'minShareCommissionRate' => 'required|integer',
            'maxShareCommissionRate' => 'required|integer',
        ];
    }
}
