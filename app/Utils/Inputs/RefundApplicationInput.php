<?php

namespace App\Utils\Inputs;

class RefundApplicationInput extends BaseInput
{
    public $type;
    public $reason;
    public $imageList;

    public function rules()
    {
        return [
            'type' => 'required|integer|in:1,2',
            'reason' => 'required|string',
            'imageList' => 'array',
        ];
    }
}
