<?php

namespace App\Utils\Inputs;

class EnterpriseInfoPageInput extends PageInput
{
    public $status;
    public $name;

    public function rules()
    {
        return array_merge([
            'status' => 'integer|in:0,1,2,3',
            'name' => 'string',
        ], parent::rules());
    }
}
