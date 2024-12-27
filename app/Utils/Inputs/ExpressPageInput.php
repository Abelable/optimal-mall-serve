<?php

namespace App\Utils\Inputs;

class ExpressPageInput extends PageInput
{
    public $name;
    public $code;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'name' => 'string',
            'code' => 'string',
        ]);
    }
}
