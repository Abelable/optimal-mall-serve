<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class BannerInput extends BaseInput
{
    public $cover;
    public $desc;
    public $scene;
    public $param;

    public function rules()
    {
        return [
            'cover' => 'required|string',
            'desc' => 'string',
            'scene' => 'integer|in:1,2',
            'param' => 'string'
        ];
    }
}
