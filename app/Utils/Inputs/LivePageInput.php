<?php

namespace App\Utils\Inputs;

class LivePageInput extends PageInput
{
    public $id;
    public $status;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'id' => 'integer|digits_between:1,20',
            'status' => 'integer',
        ]);
    }
}
