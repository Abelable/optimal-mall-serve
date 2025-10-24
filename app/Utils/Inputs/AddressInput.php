<?php

namespace App\Utils\Inputs;

class AddressInput extends BaseInput
{
    public $id;
    public $isDefault = 0;
    public $name;
    public $mobile;
    public $regionDesc;
    public $regionCodeList;
    public $addressDetail;

    public function rules()
    {
        return [
            'id' => 'integer|digits_between:1,20',
            'isDefault' => 'integer|in:0,1',
            'name' => 'required|string',
            'mobile' => 'required|regex:/^1[3-9]\d{9}$/',
            'regionDesc' => 'required|string',
            'regionCodeList' => 'required|string',
            'addressDetail' => 'required|string',
        ];
    }
}
