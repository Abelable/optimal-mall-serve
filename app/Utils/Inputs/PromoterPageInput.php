<?php

namespace App\Utils\Inputs;

class PromoterPageInput extends PageInput
{
    public $level;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'levelList' => 'integer|in:1,2,3,4,5',
        ]);
    }
}
