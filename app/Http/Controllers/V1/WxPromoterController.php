<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Utils\WxMpServe;

class WxPromoterController extends Controller
{
    public function productList()
    {
        $nextKey = $this->verifyString('nextKey', '');
        $pageSize = $this->verifyInteger('pageSize', 10);
        $planType = $this->verifyInteger('planType', 2);

        $result = WxMpServe::new()->getPromoterProductList($nextKey, $pageSize, $planType);
        $productList = collect($result['product_list'])->map(function ($item) {
            return WxMpServe::new()->getProductBaseInfo($item['product_id']);
        });

        return $this->success([
            'productList' => $productList,
            'nextKey' => $result['next_key'],
        ]);
    }
}
