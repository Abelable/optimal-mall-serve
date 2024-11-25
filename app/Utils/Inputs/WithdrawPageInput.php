<?php

namespace App\Utils\Inputs;

class WithdrawPageInput extends PageInput
{
    public $status;
    public $scene;

    public function rules()
    {
        return array_merge([
            'status' => 'integer|digits_between:1,20',
            'scene' => 'integer|digits_between:1,20',
        ], parent::rules());
    }
}
