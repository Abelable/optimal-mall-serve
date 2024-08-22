<?php

namespace App\Utils\Inputs;

class UserInfoInput extends BaseInput
{
    public $bg;
    public $avatar;
    public $nickname;
    public $gender;
    public $wxQrcode;
    public $signature;

    public function rules()
    {
        return [
            'bg' => 'string',
            'avatar' => 'string',
            'nickname' => 'string',
            'gender' => 'integer|in:0,1,2',
            'wxQrcode' => 'string',
            'signature' => 'string',
        ];
    }
}
