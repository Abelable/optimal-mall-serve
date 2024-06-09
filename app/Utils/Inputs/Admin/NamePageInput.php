<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class NamePageInput extends PageInput
{
    public $name;

    public function rules()
    {
        return array_merge([
            'name' => 'string',
        ], parent::rules());
    }
}
