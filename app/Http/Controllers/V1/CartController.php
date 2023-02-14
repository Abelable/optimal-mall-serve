<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Goods;
use App\Models\Shop;
use App\Services\CartService;
use App\Services\GoodsService;
use App\Services\ShopService;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CartAddInput;
use App\Utils\Inputs\CartEditInput;

class CartController extends Controller
{
    public function goodsNumber()
    {
        $number = CartService::getInstance()->cartGoodsNumber($this->userId());
        return $this->success($number);
    }

    public function list()
    {
        $cartColumns = [
            'id',
            'status',
            'status_desc',
            'goods_id',
            'shop_id',
            'goods_category_id',
            'goods_image',
            'goods_name',
            'selected_sku_name',
            'selected_sku_index',
            'price',
            'market_price',
            'number'
        ];
        $list = CartService::getInstance()->cartList($this->userId(), $cartColumns);
        $goodsIds = array_unique($list->pluck('goods_id')->toArray());
        $goodsCategoryIds = array_unique($list->pluck('goods_category_id')->toArray());
        $shopIds = array_unique($list->pluck('shop_id')->toArray());

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');
        $cartGoodsList = $list->map(function (Cart $cart) use ($goodsList) {
            /** @var Goods $goods */
            $goods = $goodsList->get($cart->goods_id);
            if (is_null($goods) || $goods->status != 1) {
                $cart->status = 3;
                $cart->status_desc = '商品已下架';
                $cart->save();
                return $cart;
            }
            $skuList = json_decode($goods->sku_list);
            if (count($skuList) == 0) {
                if ($cart->number > $goods->stock) {
                    if ($goods->stock != 0) {
                        $cart->number = $goods->stock;
                        $cart->save();
                        $cart['stock'] = $goods->stock;
                    } else {
                        $cart->status = 3;
                        $cart->status_desc = '商品暂无库存';
                        $cart->save();
                    }
                } else {
                    $cart['stock'] = $goods->stock;
                }
                return $cart;
            }
            $sku = $skuList[$cart->selected_sku_index];
            if (is_null($sku) || $cart->selected_sku_name != $sku->name) {
                $cart->status = 2;
                $cart->status_desc = '商品规格不存在';
                $cart->selected_sku_index = -1;
                $cart->selected_sku_name = '';
                $cart->save();
                return $cart;
            }
            if ($cart->number > $sku->stock) {
                if ($sku->stock != 0) {
                    $cart->number = $sku->stock;
                    $cart->save();
                    $cart['stock'] = $sku->stock;
                } else {
                    $cart->status = 2;
                    $cart->status_desc = '当前规格暂无库存';
                    $cart->selected_sku_index = -1;
                    $cart->selected_sku_name = '';
                    $cart->save();
                }
            } else {
                $cart['stock'] = $sku->stock;
            }
            return $cart;
        });

        $shopList = ShopService::getInstance()->getShopListByIds($shopIds, ['id', 'avatar', 'name']);
        $cartList = $shopList->map(function (Shop $shop) use ($cartGoodsList) {
            return [
                'shopInfo' => $shop,
                'goodsList' => $cartGoodsList->filter(function (Cart $cart) use ($shop) {
                    return $cart->shop_id == $shop->id;
                })->map(function (Cart $cart) {
                    unset($cart->shop_id);
                    unset($cart->goods_category_id);
                    return $cart;
                })
            ];
        });
        if (in_array(0, $shopIds)) {
            $cartList->prepend([
                'goodsList' => $cartGoodsList->filter(function (Cart $cart) {
                    return $cart->shop_id == 0;
                })->map(function (Cart $cart) {
                    unset($cart->shop_id);
                    unset($cart->goods_category_id);
                    return $cart;
                })
            ]);
        }

        $recommendGoodsList = GoodsService::getInstance()->getRecommendGoodsList($goodsIds, $goodsCategoryIds);

        return $this->success([
            'cartList' => $cartList,
            'recommendGoodsList' => $recommendGoodsList
        ]);
    }

    public function fastAdd()
    {
        /** @var CartAddInput $input */
        $input = CartAddInput::new();
        $cart = CartService::getInstance()->addCart($this->userId(), $input, 2);
        return $this->success($cart->id);
    }

    public function add()
    {
        /** @var CartAddInput $input */
        $input = CartAddInput::new();
        CartService::getInstance()->addCart($this->userId(), $input);
        return $this->goodsNumber();
    }

    public function edit()
    {
        /** @var CartEditInput $input */
        $input = CartEditInput::new();
        $cart = CartService::getInstance()->editCart($input);
        return $this->success([
            'status' => $cart->status,
            'statusDesc' => $cart->status_desc,
            'selectedSkuIndex' => $cart->selected_sku_index,
            'selectedSkuName' => $cart->selected_sku_name,
            'price' => $cart->price,
            'number' => $cart->number,
            'stock' => $cart['stock'],
        ]);
    }

    public function delete()
    {
        $ids = $this->verifyArrayNotEmpty('ids', []);
        CartService::getInstance()->deleteCartList($this->userId(), $ids);
        return $this->success();;
    }
}
