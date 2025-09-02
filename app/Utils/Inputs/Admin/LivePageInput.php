<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\PageInput;

class LivePageInput extends PageInput
{
    public $title;
    public $status;
    public $userId;

    public function rules()
    {
        return array_merge([
            'title' => 'string',
            'status' => 'integer|in:0,1,2,3',
            'userId' => 'integer|digits_between:1,20',
        ], parent::rules());
    }
}
