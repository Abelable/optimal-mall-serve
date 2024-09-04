<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\CartGoods;
use App\Models\Goods;
use App\Services\CartGoodsService;
use App\Services\GoodsService;
use App\Utils\Inputs\CartGoodsInput;
use App\Utils\Inputs\CartGoodsEditInput;

class CartController extends Controller
{
    public function goodsNumber()
    {
        $number = CartGoodsService::getInstance()->cartGoodsNumber($this->userId());
        return $this->success((int) $number);
    }

    public function list()
    {
        $cartGoodsColumns = [
            'id',
            'status',
            'status_desc',
            'goods_id',
            'freight_template_id',
            'cover',
            'name',
            'selected_sku_name',
            'selected_sku_index',
            'price',
            'market_price',
            'number',
            'created_at'
        ];
        $list = CartGoodsService::getInstance()->cartGoodsList($this->userId(), $cartGoodsColumns);
        $goodsIds = array_unique($list->pluck('goods_id')->toArray());

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');
        $cartGoodsList = $list->map(function (CartGoods $cartGoods) use ($goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($cartGoods->goods_id);
            $cartGoods['categoryIds'] = $goods->categories->pluck('category_id')->toArray();
            if (is_null($goods) || $goods->status != 1) {
                $cartGoods->status = 3;
                $cartGoods->status_desc = '商品已下架';
                $cartGoods->save();
                return $cartGoods;
            }
            $skuList = json_decode($goods->sku_list);
            if (count($skuList) == 0) {
                if ($cartGoods->number > $goods->stock) {
                    if ($goods->stock != 0) {
                        $cartGoods->number = $goods->stock;
                        $cartGoods->save();
                        $cartGoods['stock'] = $goods->stock;
                    } else {
                        $cartGoods->status = 3;
                        $cartGoods->status_desc = '商品暂无库存';
                        $cartGoods->save();
                    }
                } else {
                    $cartGoods['stock'] = $goods->stock;
                }
                return $cartGoods;
            }
            $sku = $skuList[$cartGoods->selected_sku_index];
            if (is_null($sku) || $cartGoods->selected_sku_name != $sku->name) {
                $cartGoods->status = 2;
                $cartGoods->status_desc = '商品规格不存在';
                $cartGoods->selected_sku_index = -1;
                $cartGoods->selected_sku_name = '';
                $cartGoods->save();
                return $cartGoods;
            }
            if ($cartGoods->number > $sku->stock) {
                if ($sku->stock != 0) {
                    $cartGoods->number = $sku->stock;
                    $cartGoods->save();
                    $cartGoods['stock'] = $sku->stock;
                } else {
                    $cartGoods->status = 2;
                    $cartGoods->status_desc = '当前规格暂无库存';
                    $cartGoods->selected_sku_index = -1;
                    $cartGoods->selected_sku_name = '';
                    $cartGoods->save();
                }
            } else {
                $cartGoods['stock'] = $sku->stock;
            }
            return $cartGoods;
        });

        return $this->success($cartGoodsList);
    }

    public function fastAdd()
    {
        /** @var CartGoodsInput $input */
        $input = CartGoodsInput::new();
        $cartGoods = CartGoodsService::getInstance()->addCartGoods($this->userId(), $input, 2);
        return $this->success($cartGoods->id);
    }

    public function add()
    {
        /** @var CartGoodsInput $input */
        $input = CartGoodsInput::new();
        CartGoodsService::getInstance()->addCartGoods($this->userId(), $input);
        return $this->goodsNumber();
    }

    public function edit()
    {
        /** @var CartGoodsEditInput $input */
        $input = CartGoodsEditInput::new();
        $cartGoods = CartGoodsService::getInstance()->editCartGoods($input);
        return $this->success([
            'status' => $cartGoods->status,
            'statusDesc' => $cartGoods->status_desc,
            'selectedSkuIndex' => $cartGoods->selected_sku_index,
            'selectedSkuName' => $cartGoods->selected_sku_name,
            'price' => $cartGoods->price,
            'number' => $cartGoods->number,
            'stock' => $cartGoods['stock'],
        ]);
    }

    public function delete()
    {
        $ids = $this->verifyArrayNotEmpty('ids', []);
        CartGoodsService::getInstance()->deleteCartGoodsList($this->userId(), $ids);
        return $this->success();
    }
}
