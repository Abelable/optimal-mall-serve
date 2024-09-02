<?php

namespace App\Utils\Inputs\Admin;

use App\Utils\Inputs\BaseInput;

class ActivityEditInput extends BaseInput
{
    public $id;
    public $status;
    public $name;
    public $startTime;
    public $endTime;

    public function rules()
    {
        return [
            'id' => 'required|integer|digits_between:1,20',
            'status' => 'required|integer|in:0,1',
            'name' => 'required|string',
            'startTime' => 'string',
            'endTime' => 'string'
        ];
    }
}
