<?php

namespace App\Utils\Inputs;

class WithdrawalPageInput extends PageInput
{
    public $status;
    public $scene;
    public $path;

    public function rules()
    {
        return array_merge([
            'status' => 'integer|in:0,1,2',
            'scene' => 'integer|in:1,2,3',
            'path' => 'integer|in:1,2,3',
        ], parent::rules());
    }
}
