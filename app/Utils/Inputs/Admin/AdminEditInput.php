<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class AdminEditInput extends BaseInput
{
    public $id;
    public $avatar;
    public $nickname;
    public $roleId;
    public $password;

    public function rules()
    {
        return [
            'id' => 'required|integer|digits_between:1,20',
            'avatar' => 'string',
            'nickname' => 'string',
            'roleId' => 'required|integer|digits_between:1,20',
            'password' => 'string',
        ];
    }
}
