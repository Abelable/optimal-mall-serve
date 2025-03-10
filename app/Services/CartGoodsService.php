<?php

namespace App\Services;

use App\Models\CartGoods;
use App\Utils\CodeResponse;
use App\Utils\Inputs\CartGoodsInput;
use App\Utils\Inputs\CartGoodsEditInput;

class CartGoodsService extends BaseService
{
    public function cartGoodsNumber($userId)
    {
        return CartGoods::query()->where('user_id', $userId)->where('scene', '1')->sum('number');
    }

    public function cartGoodsNumberByGoodsIds($userId, array $goodsIds)
    {
        return CartGoods::query()->where('scene', '1')->where('user_id', $userId)->whereIn('goods_id', $goodsIds)->sum('number');
    }

    public function cartGoodsList($userId, $columns = ['*'])
    {
        return CartGoods::query()->where('user_id', $userId)->where('scene', '1')->orderBy('updated_at', 'desc')->get($columns);
    }

    public function getCartGoodsListByIds($userId, array $ids, $columns = ['*'])
    {
        return CartGoods::query()->where('user_id', $userId)->whereIn('id', $ids)->get($columns);
    }

    public function addCartGoods($userId, CartGoodsInput $input, $scene = 1)
    {
        $goodsId = $input->goodsId;
        $selectedSkuIndex = $input->selectedSkuIndex;
        $number = $input->number;

        $goods = GoodsService::getInstance()->getOnSaleGoods($goodsId);
        if (is_null($goods)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '当前商品不存在');
        }
        $skuList = json_decode($goods->sku_list);
        if (count($skuList) != 0 && $selectedSkuIndex != -1) {
            $stock = $skuList[$selectedSkuIndex]->stock;
            if ($stock == 0 || $number > $stock) {
                $this->throwBusinessException(CodeResponse::CART_INVALID_OPERATION, '所选规格库存不足');
            }
        }
        if ($goods->stock == 0 || $number > $goods->stock) {
            $this->throwBusinessException(CodeResponse::CART_INVALID_OPERATION, '商品库存不足');
        }

        $cartGoods = $this->getExistCartGoods($userId, $goodsId, $selectedSkuIndex, $scene);
        if (!is_null($cartGoods) && $scene == 1) {
            $cartGoods->number = $cartGoods->number + $number;
        } else {
            if (!is_null($cartGoods) && $scene == 2) {
                $cartGoods->delete();
            }
            $giftGoodsIds = GiftGoodsService::getInstance()->getGoodsList([1, 2])->pluck('goods_id')->toArray();

            $cartGoods = CartGoods::new();
            $cartGoods->scene = $scene;
            $cartGoods->user_id = $userId;
            $cartGoods->goods_id = $goodsId;
            $cartGoods->is_gift = in_array($goodsId, $giftGoodsIds) ? 1 : 0;
            $cartGoods->merchant_id = $goods->merchant_id;
            $cartGoods->freight_template_id = $goods->freight_template_id;
            $cartGoods->refund_status = $goods->refund_status;
            $cartGoods->delivery_method = $goods->delivery_method;
            $cartGoods->cover = $goods->cover;
            $cartGoods->name = $goods->name;
            if (count($skuList) != 0 && $selectedSkuIndex != -1) {
                $cartGoods->selected_sku_index = $selectedSkuIndex;
                $cartGoods->selected_sku_name = $skuList[$selectedSkuIndex]->name;
                $cartGoods->price = $skuList[$selectedSkuIndex]->price;
                $cartGoods->commission_rate = $skuList[$selectedSkuIndex]->commissionRate ?? $goods->commission_rate;
            } else {
                $cartGoods->price = $goods->price;
                $cartGoods->commission_rate = $goods->commission_rate;
            }
            $cartGoods->market_price = $goods->market_price;
            $cartGoods->number = $number;
        }
        $cartGoods->save();

        return $cartGoods;
    }

    public function editCartGoods($userId, CartGoodsEditInput $input)
    {
        $cartGoodsId = $input->id;
        $goodsId = $input->goodsId;
        $selectedSkuIndex = $input->selectedSkuIndex;
        $number = $input->number;

        $cartGoods = $this->getExistCartGoods($userId, $goodsId, $selectedSkuIndex, 1, $cartGoodsId);
        if (!is_null($cartGoods)) {
            $this->throwBusinessException(CodeResponse::DATA_EXISTED, '购物车中已存在当前规格商品');
        }

        $goods = GoodsService::getInstance()->getOnSaleGoods($goodsId);
        if (is_null($goods)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '当前商品不存在');
        }

        $skuList = json_decode($goods->sku_list);
        if (count($skuList) != 0 && $selectedSkuIndex != -1) {
            $stock = $skuList[$selectedSkuIndex]->stock;
            if ($stock == 0 || $number > $stock) {
                $this->throwBusinessException(CodeResponse::CART_INVALID_OPERATION, '所选规格库存不足');
            }
        }
        if ($goods->stock == 0 || $number > $goods->stock) {
            $this->throwBusinessException(CodeResponse::CART_INVALID_OPERATION, '商品库存不足');
        }

        $cartGoods = $this->getCartGoodsById($cartGoodsId);
        if (is_null($cartGoods)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '购物车中未添加该商品');
        }
        if ($cartGoods->status == 3) {
            $this->throwBusinessException(CodeResponse::CART_INVALID_OPERATION, '购物车商品已下架，无法编辑');
        }

        if (count($skuList) != 0 && $selectedSkuIndex != -1) {
            $cartGoods->selected_sku_index = $selectedSkuIndex;
            $cartGoods->selected_sku_name = $skuList[$selectedSkuIndex]->name;
            $cartGoods->price = $skuList[$selectedSkuIndex]->price;
            $cartGoods->market_price = $skuList[$selectedSkuIndex]->originalPrice ?? $goods->market_price;
            $cartGoods->commission_rate = $skuList[$selectedSkuIndex]->commissionRate ?? $goods->commission_rate;
        }

        $cartGoods->number = $number;
        if ($cartGoods->status == 2) {
            $cartGoods->status = 1;
            $cartGoods->status_desc = '';
        }
        $cartGoods->save();

        // 限购逻辑
        $orderGoodsList = OrderGoodsService::getInstance()->getRecentlyUserListByGoodsIds($userId, [$goodsId]);
        $userPurchasedList = collect($orderGoodsList)->groupBy(function ($item) {
            return $item['selected_sku_name'] . '|' . $item['selected_sku_index'];
        })->map(function ($groupedItems) {
            return [
                'selected_sku_name' => $groupedItems->first()['selected_sku_name'],
                'selected_sku_index' => $groupedItems->first()['selected_sku_index'],
                'number' => $groupedItems->sum('number'),
            ];
        });
        if (count($skuList) != 0 && $selectedSkuIndex != -1) {
            $sku = $skuList[$selectedSkuIndex];
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
            if ($goods->number_limit != 0) {
                $userPurchasedNumber = $userPurchasedList->first()['number'] ?? 0;
                $cartGoods['numberLimit'] = min($goods->number_limit, $goods->stock) - $userPurchasedNumber;
            } else {
                $cartGoods['numberLimit'] = $goods->stock;
            }
        }

        return $cartGoods;
    }

    public function getExistCartGoods($userId, $goodsId, $selectedSkuIndex, $scene, $id = 0, $columns = ['*'])
    {
        $query = CartGoods::query();
        if ($id != 0) {
            $query = $query->where('id', '!=', $id);
        }
        return $query
            ->where('user_id', $userId)
            ->where('goods_id', $goodsId)
            ->where('selected_sku_index', $selectedSkuIndex)
            ->where('scene', $scene)
            ->first($columns);
    }

    public function getCartGoodsById($id, $columns = ['*'])
    {
        return CartGoods::query()->find($id, $columns);
    }

    public function deleteCartGoodsList($userId, array $ids)
    {
        return CartGoods::query()->where('user_id', $userId)->whereIn('id', $ids)->delete();
    }

    public function getListByGoodsId($userId, $goodsId, $columns = ['*'])
    {
        return CartGoods::query()->where('user_id', $userId)->where('scene', '1')->where('goods_id', $goodsId)->get($columns);
    }
}
