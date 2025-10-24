<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class MerchantListInput extends PageInput
{
    public $name;
    public $consigneeName;
    public $mobile;

    public function rules()
    {
        return array_merge([
            'name' => 'string',
            'consigneeName' => 'string',
            'mobile' => 'regex:/^1[3-9]\d{9}$/',
        ], parent::rules());
    }
}
