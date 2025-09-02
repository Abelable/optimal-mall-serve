<?php

namespace App\Utils\Inputs;

class LivePageInput extends PageInput
{
    public $roomId;
    public $status;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'roomId' => 'integer|digits_between:1,20',
            'status' => 'integer',
        ]);
    }
}
