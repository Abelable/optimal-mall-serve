<?php

namespace App\Utils\Inputs;

class GoodsInput extends BaseInput
{
    public $cover;
    public $imageList;
    public $detailImageList;
    public $defaultSpecImage;
    public $name;
    public $freightTemplateId;
    public $categoryId;
    public $returnAddressId;
    public $price;
    public $marketPrice;
    public $stock;
    public $leaderCommissionRate;
    public $shareCommissionRate;
    public $specList;
    public $skuList;

    public function rules()
    {
        return [
            'cover' => 'required|string',
            'imageList' => 'required|array',
            'detailImageList' => 'required|array',
            'defaultSpecImage' => 'required|string',
            'name' => 'required|string',
            'freightTemplateId' => 'required|integer|digits_between:1,20',
            'categoryId' => 'required|integer|digits_between:1,20',
            'returnAddressId' => 'required|integer|digits_between:1,20',
            'price' => 'required|numeric',
            'marketPrice' => 'numeric',
            'stock' => 'required|integer',
            'leaderCommissionRate' => 'required|numeric',
            'shareCommissionRate' => 'required|numeric',
            'specList' => 'required|array',
            'skuList' => 'required|array',
        ];
    }
}
