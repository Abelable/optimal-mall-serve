<?php

namespace App\Utils\Inputs;

class UserLevelPageInput extends PageInput
{
    public $levelList;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'levelList' => 'array',
        ]);
    }
}
