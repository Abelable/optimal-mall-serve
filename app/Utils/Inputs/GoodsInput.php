<?php

namespace App\Utils\Inputs;

class GoodsInput extends BaseInput
{
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
    public $categoryIds;
    public $merchantId;
    public $price;
    public $marketPrice;
    public $stock;
    public $commissionRate;
    public $numberLimit;
    public $deliveryMethod;
    public $pickupAddressIds;
    public $refundStatus;
    public $refundAddressIds;
    public $specList;
    public $skuList;

    public function rules()
    {
        return [
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
            'categoryIds' => 'required|array',
            'merchantId' => 'required|integer|digits_between:1,20',
            'price' => 'required|numeric',
            'marketPrice' => 'numeric',
            'stock' => 'required|integer',
            'commissionRate' => 'numeric',
            'numberLimit' => 'integer|digits_between:1,20',
            'deliveryMethod' => 'required|integer|in:1,2,3',
            'pickupAddressIds' => 'array',
            'refundStatus' => 'required|integer|in:0,1',
            'refundAddressIds' => 'array',
            'specList' => 'array',
            'skuList' => 'array',
        ];
    }
}
