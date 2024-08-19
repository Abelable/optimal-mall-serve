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
    public $categoryIds;
    public $merchantId;
    public $price;
    public $marketPrice;
    public $stock;
    public $commissionRate;
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
            'categoryIds' => 'required|string',
            'merchantId' => 'required|integer|digits_between:1,20',
            'price' => 'required|numeric',
            'marketPrice' => 'numeric',
            'stock' => 'required|integer',
            'commissionRate' => 'required|numeric',
            'specList' => 'array',
            'skuList' => 'array',
        ];
    }
}
