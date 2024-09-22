<?php

namespace App\Utils\Inputs;

class SearchPageInput extends PageInput
{
    public $keywords;

    public function rules()
    {
        return array_merge(parent::rules(), [
            'keywords' => 'string',
        ]);
    }
}
