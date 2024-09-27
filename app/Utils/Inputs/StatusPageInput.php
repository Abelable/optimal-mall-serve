<?php

namespace App\Utils\Inputs;

class StatusPageInput extends PageInput
{
    public $status;

    public function rules()
    {
        return array_merge([
            'status' => 'integer|digits_between:1,20',
        ], parent::rules());
    }
}
