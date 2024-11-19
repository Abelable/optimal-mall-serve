<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class UserPageInput extends PageInput
{
    public $nickname;
    public $mobile;
    public $level;
    public $superiorId;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'nickname' => 'string',
            'mobile' => 'regex:/^1[345789][0-9]{9}$/',
            'level' => 'integer',
            'superiorId' => 'integer',
        ]);
    }
}
