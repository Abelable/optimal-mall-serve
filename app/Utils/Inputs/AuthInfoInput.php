<?php

namespace App\Utils\Inputs;

class AuthInfoInput extends BaseInput
{
    public $name;
    public $mobile;
    public $idCardNumber;
    public $idCardFrontPhoto;
    public $idCardBackPhoto;
    public $holdIdCardPhoto;

    public function rules()
    {
        return [
            'name' => 'required|string',
            'mobile' => 'required|regex:/^1[3-9]\d{9}$/',
            'idCardNumber' => 'required|string',
            'idCardFrontPhoto' => 'required|string',
            'idCardBackPhoto' => 'required|string',
            'holdIdCardPhoto' => 'required|string',
        ];
    }
}
