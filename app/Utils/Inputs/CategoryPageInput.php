<?php

namespace App\Utils\Inputs;

class CategoryPageInput extends PageInput
{
    public $categoryId;

    public function rules()
    {
        return array_merge([
            'categoryId' => 'integer',
        ], parent::rules());
    }
}
