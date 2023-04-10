<?php

namespace App\Utils\Inputs;

class ScenicProviderListInput extends PageInput
{
    public $name;
    public $mobile;

    public function rules()
    {
        return array_merge([
            'name' => 'string',
            'mobile' => 'regex:/^1[345789][0-9]{9}$/',
        ], parent::rules());
    }
}
