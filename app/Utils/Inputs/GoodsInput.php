<?php

namespace App\Utils\Inputs;

class GoodsInput extends BaseInput
{
    public $video;
    public $cover;
    public $imageList;
    public $detailImageList;
    public $defaultSpecImage;
    public $name;
    public $introduction;
    public $freightTemplateId;
    public $categoryId;
    public $merchantId;
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
            'video' => 'string',
            'cover' => 'required|string',
            'imageList' => 'required|array',
            'detailImageList' => 'required|array',
            'defaultSpecImage' => 'required|string',
            'name' => 'required|string',
            'introduction' => 'string',
            'freightTemplateId' => 'required|integer|digits_between:1,20',
            'categoryId' => 'required|integer|digits_between:1,20',
            'merchantId' => 'required|integer|digits_between:1,20',
            'price' => 'required|numeric',
            'marketPrice' => 'numeric',
            'stock' => 'required|integer',
            'leaderCommissionRate' => 'required|numeric',
            'shareCommissionRate' => 'required|numeric',
            'specList' => 'array',
            'skuList' => 'array',
        ];
    }
}
