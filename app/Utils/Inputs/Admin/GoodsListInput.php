<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class GoodsListInput extends PageInput
{
    public $name;
    public $status;
    public $merchantId;
    public $categoryId;

    public function rules()
    {
        return array_merge([
            'name' => 'string',
            'status' => 'integer|in:0,1,2,3',
            'merchantId' => 'integer|digits_between:1,20',
            'categoryId' => 'integer|digits_between:1,20',
        ], parent::rules());
    }
}
