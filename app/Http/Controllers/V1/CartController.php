<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\CartGoods;
use App\Models\Goods;
use App\Services\ActivityService;
use App\Services\CartGoodsService;
use App\Services\GoodsService;
use App\Services\NewYearCultureGoodsService;
use App\Services\NewYearGoodsService;
use App\Services\NewYearLocalGoodsService;
use App\Services\OrderGoodsService;
use App\Utils\Inputs\CartGoodsInput;
use App\Utils\Inputs\CartGoodsEditInput;

class CartController extends Controller
{
    public function goodsNumber()
    {
        $number = CartGoodsService::getInstance()->cartGoodsNumber($this->userId());
        return $this->success((int)$number);
    }

    public function newYearGoodsNumber()
    {
        $newYearGoodsIds = NewYearGoodsService::getInstance()->getGoodsList()->pluck('goods_id')->toArray();
        $newYearCultureGoodsIds = NewYearCultureGoodsService::getInstance()->getGoodsList()->pluck('goods_id')->toArray();
        $newYearLocalGoodsIds = NewYearLocalGoodsService::getInstance()->getAllGoodsList()->pluck('goods_id')->toArray();
        $mergedGoodsIds = array_unique(array_merge($newYearGoodsIds, $newYearCultureGoodsIds, $newYearLocalGoodsIds));
        $number = CartGoodsService::getInstance()->cartGoodsNumberByGoodsIds($this->userId(), $mergedGoodsIds);
        return $this->success((int)$number);
    }

    public function list()
    {
        $cartGoodsColumns = [
            'id',
            'status',
            'status_desc',
            'goods_id',
            'is_gift',
            'freight_template_id',
            'cover',
            'name',
            'selected_sku_name',
            'selected_sku_index',
            'price',
            'market_price',
            'commission_rate',
            'number',
            'created_at',
            'updated_at',
        ];
        $list = CartGoodsService::getInstance()->cartGoodsList($this->userId(), $cartGoodsColumns);
        $goodsIds = array_unique($list->pluck('goods_id')->toArray());

        $goodsList = GoodsService::getInstance()->getGoodsListByIds($goodsIds)->keyBy('id');
        $groupedOrderGoodsList = OrderGoodsService::getInstance()->getRecentlyUserListByGoodsIds($this->userId(), $goodsIds)->groupBy('goods_id');

        $noticeGoodsIds = ActivityService::getInstance()->getActivityListByStatus(0)->pluck('goods_id')->toArray();

        // 年货节
        // $newYearGoodsIds = NewYearGoodsService::getInstance()->getGoodsList()->pluck('goods_id')->toArray();
        // $newYearCultureGoodsIds = NewYearCultureGoodsService::getInstance()->getGoodsList()->pluck('goods_id')->toArray();
        // $newYearLocalGoodsIds = NewYearLocalGoodsService::getInstance()->getAllGoodsList()->pluck('goods_id')->toArray();
        // $newYearGoodsIds = array_unique(array_merge($newYearGoodsIds, $newYearCultureGoodsIds, $newYearLocalGoodsIds));

        $cartGoodsList = $list->map(function (CartGoods $cartGoods) use ($noticeGoodsIds, $goodsList, $groupedOrderGoodsList) {
            // todo 暂定删除活动商品，之后优化
            if (in_array($cartGoods->goods_id, $noticeGoodsIds)) {
                $cartGoods->delete();
                return null;
            }

            if ($cartGoods->status != 1) {
                return $cartGoods;
            }

            /** @var Goods $goods */
            $goods = $goodsList->get($cartGoods->goods_id);
            if (is_null($goods) || $goods->status != 1) {
                $cartGoods->status = 3;
                $cartGoods->status_desc = '商品已下架';
                $cartGoods->save();
                return $cartGoods;
            }
            if ($goods->stock == 0) {
                $cartGoods->status = 3;
                $cartGoods->status_desc = '商品暂无库存';
                $cartGoods->save();
                return $cartGoods;
            }

            // 限购逻辑
            $orderGoodsList = $groupedOrderGoodsList->get($cartGoods->goods_id);
            $userPurchasedList = collect($orderGoodsList)->groupBy(function ($item) {
                return $item['selected_sku_name'] . '|' . $item['selected_sku_index'];
            })->map(function ($groupedItems) {
                return [
                    'selected_sku_name' => $groupedItems->first()['selected_sku_name'],
                    'selected_sku_index' => $groupedItems->first()['selected_sku_index'],
                    'number' => $groupedItems->sum('number'),
                ];
            });

            $skuList = json_decode($goods->sku_list);
            if (count($skuList) != 0) {
                $sku = $skuList[$cartGoods->selected_sku_index];
                if (is_null($sku) || $cartGoods->selected_sku_name != $sku->name) {
                    $cartGoods->status = 2;
                    $cartGoods->status_desc = '商品规格不存在';
                    $cartGoods->selected_sku_index = -1;
                    $cartGoods->selected_sku_name = '';
                    $cartGoods->save();
                    return $cartGoods;
                }
                if ($sku->stock == 0) {
                    $cartGoods->status = 2;
                    $cartGoods->status_desc = '当前规格暂无库存';
                    $cartGoods->selected_sku_index = -1;
                    $cartGoods->selected_sku_name = '';
                    $cartGoods->save();
                    return $cartGoods;
                }

                if ($cartGoods->price != $sku->price) {
                    $cartGoods->price = $sku->price;
                    $cartGoods->save();
                }
                if (isset($sku->originalPrice) && $cartGoods->market_price != $sku->originalPrice) {
                    $cartGoods->market_price = $sku->originalPrice;
                    $cartGoods->save();
                }
                if (isset($sku->commissionRate) && $cartGoods->commission_rate != $sku->commissionRate) {
                    $cartGoods->commission_rate = $sku->commissionRate;
                    $cartGoods->save();
                }
                if ($cartGoods->number > $sku->stock) {
                    $cartGoods->number = $sku->stock;
                    $cartGoods->save();
                }

                // 限购逻辑
                $numberLimit = $sku->limit ?? $goods->number_limit;
                $stock = $sku->stock ?? $goods->stock;
                if ($numberLimit != 0) {
                    $userPurchasedNumber = $userPurchasedList->filter(function ($item) use ($cartGoods) {
                        return $item['selected_sku_index'] == $cartGoods->selected_sku_index
                            && $item['selected_sku_name'] == $cartGoods->selected_sku_name;
                    })->first()['number'] ?? 0;
                    $cartGoods['numberLimit'] = min($numberLimit, $stock) - $userPurchasedNumber;
                } else {
                    $cartGoods['numberLimit'] = $stock;
                }
            } else {
                if ($cartGoods->price != $goods->price) {
                    $cartGoods->price = $goods->price;
                    $cartGoods->save();
                }
                if ($cartGoods->market_price != $goods->market_price) {
                    $cartGoods->market_price = $goods->market_price;
                    $cartGoods->save();
                }
                if ($cartGoods->commission_rate != $goods->commission_rate) {
                    $cartGoods->commission_rate = $goods->commission_rate;
                    $cartGoods->save();
                }
                if ($cartGoods->number > $goods->stock) {
                    $cartGoods->number = $goods->stock;
                    $cartGoods->save();
                }

                // 限购逻辑
                if ($goods->number_limit != 0) {
                    $userPurchasedNumber = $userPurchasedList->first()['number'] ?? 0;
                    $cartGoods['numberLimit'] = min($goods->number_limit, $goods->stock) - $userPurchasedNumber;
                } else {
                    $cartGoods['numberLimit'] = $goods->stock;
                }
            }

            $cartGoods['categoryIds'] = $goods->categories->pluck('category_id')->toArray();

            // 年货节
            // $cartGoods['isNewYearGift'] = in_array($cartGoods->goods_id, $newYearGoodsIds) ? 1 : 0;

            return $cartGoods;
        })->filter(function ($cartGoods) {
            return !is_null($cartGoods);
        })->values();

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
        $cartGoods = CartGoodsService::getInstance()->editCartGoods($this->userId(), $input);
        return $this->success([
            'status' => $cartGoods->status,
            'statusDesc' => $cartGoods->status_desc,
            'selectedSkuIndex' => $cartGoods->selected_sku_index,
            'selectedSkuName' => $cartGoods->selected_sku_name,
            'price' => $cartGoods->price,
            'marketPrice' => $cartGoods->market_price,
            'number' => $cartGoods->number,
            'numberLimit' => $cartGoods['numberLimit'],
        ]);
    }

    public function delete()
    {
        $ids = $this->verifyArrayNotEmpty('ids', []);
        CartGoodsService::getInstance()->deleteCartGoodsList($this->userId(), $ids);
        return $this->success();
    }
}
