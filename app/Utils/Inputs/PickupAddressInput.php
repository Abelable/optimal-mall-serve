<?php

namespace App\Utils\Inputs;

class PickupAddressInput extends BaseInput
{
    public $merchantId;
    public $name;
    public $timeFrame;
    public $addressDetail;
    public $longitude;
    public $latitude;

    public function rules()
    {
        return [
            'merchantId' => 'required|integer|digits_between:1,20',
            'name' => 'required|string',
            'timeFrame' => 'string',
            'addressDetail' => 'required|string',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
        ];
    }
}
