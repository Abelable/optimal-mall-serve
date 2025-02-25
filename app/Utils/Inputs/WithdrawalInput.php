<?php

namespace App\Utils\Inputs;

class WithdrawalInput extends BaseInput
{
    public $scene;
    public $withdrawAmount;
    public $path;
    public $remark;

    public function rules()
    {
        return [
            'scene' => 'required|integer|in:1,2,3',
            'withdrawAmount' => 'required|numeric',
            'path' => 'required|integer|in:1,2,3',
            'remark' => 'string',
        ];
    }
}
