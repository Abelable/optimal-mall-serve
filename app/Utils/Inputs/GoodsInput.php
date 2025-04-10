<?php

namespace App\Utils\Inputs;

class GoodsInput extends BaseInput
{
    public $merchantId;
    public $categoryIds;
    public $video;
    public $cover;
    public $activityCover;
    public $imageList;
    public $detailImageList;
    public $realImageList;
    public $defaultSpecImage;
    public $name;
    public $introduction;
    public $freightTemplateId;
    public $price;
    public $marketPrice;
    public $commissionRate;
    public $stock;
    public $numberLimit;
    public $specList;
    public $skuList;
    public $deliveryMethod;
    public $pickupAddressIds;
    public $refundStatus;
    public $refundAddressIds;

    public function rules()
    {
        return [
            'merchantId' => 'required|integer|digits_between:1,20',
            'categoryIds' => 'required|array',
            'video' => 'string',
            'cover' => 'required|string',
            'activityCover' => 'string',
            'imageList' => 'required|array',
            'detailImageList' => 'required|array',
            'realImageList' => 'array',
            'defaultSpecImage' => 'required|string',
            'name' => 'required|string',
            'introduction' => 'string',
            'freightTemplateId' => 'required|integer|digits_between:1,20',
            'price' => 'required|numeric',
            'marketPrice' => 'numeric',
            'commissionRate' => 'numeric',
            'stock' => 'required|integer',
            'numberLimit' => 'integer|digits_between:1,20',
            'specList' => 'array',
            'skuList' => 'array',
            'deliveryMethod' => 'required|integer|in:1,2,3',
            'pickupAddressIds' => 'array',
            'refundStatus' => 'required|integer|in:0,1',
            'refundAddressIds' => 'array',
        ];
    }
}
