<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class BannerInput extends BaseInput
{
    public $cover;
    public $desc;
    public $scene;
    public $param;
    public $position;

    public function rules()
    {
        return [
            'cover' => 'required|string',
            'desc' => 'string',
            'scene' => 'integer',
            'param' => 'string',
            'position' => 'integer',
        ];
    }
}
