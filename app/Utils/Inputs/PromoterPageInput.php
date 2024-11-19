<?php

namespace App\Utils\Inputs;

class PromoterPageInput extends PageInput
{
    public $nickname;
    public $mobile;
    public $level;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'nickname' => 'string',
            'mobile' => 'regex:/^1[345789][0-9]{9}$/',
            'level' => 'integer|in:1,2,3,4,5',
        ]);
    }
}
