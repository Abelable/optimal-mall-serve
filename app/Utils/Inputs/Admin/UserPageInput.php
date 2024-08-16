<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class UserPageInput extends PageInput
{
    public $nickname;
    public $mobile;

    public function rules()
    {
        return array_merge([
            'nickname' => 'string',
            'mobile' => 'regex:/^1[345789][0-9]{9}$/',
        ], parent::rules());
    }
}
