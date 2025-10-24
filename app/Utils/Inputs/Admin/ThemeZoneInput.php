<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class ThemeZoneInput extends BaseInput
{
    public $name;
    public $cover;
    public $bg;
    public $scene;
    public $param;

    public function rules()
    {
        return [
            'name' => 'required|string',
            'cover' => 'required|string',
            'bg' => 'string',
            'scene' => 'required|integer',
            'param' => 'string',
        ];
    }
}
