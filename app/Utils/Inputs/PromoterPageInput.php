<?php

namespace App\Utils\Inputs;

class PromoterPageInput extends PageInput
{
    public $levelList;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'levelList' => 'array',
        ]);
    }
}
