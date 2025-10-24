<?php

namespace App\Utils\Inputs;

class AuthInfoPageInput extends PageInput
{
    public $status;
    public $name;
    public $mobile;

    public function rules()
    {
        return array_merge([
            'status' => 'integer|in:0,1,2,3',
            'name' => 'string',
            'mobile' => 'regex:/^1[3-9]\d{9}$/',
        ], parent::rules());
    }
}
