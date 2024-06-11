<?php

namespace App\Utils\Inputs;

class TeamLeaderPageInput extends PageInput
{
    public $status;
    public $name;
    public $mobile;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'status' => 'integer|in:0,1,2',
            'name' => 'string',
            'mobile' => 'regex:/^1[345789][0-9]{9}$/',
        ]);
    }
}
