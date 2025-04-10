<?php

namespace App\Services;

use App\Models\Goods;
use App\Utils\CodeResponse;
use App\Utils\Inputs\Admin\GoodsListInput;
use App\Utils\Inputs\GoodsInput;
use App\Utils\Inputs\GoodsPageInput;
use App\Utils\Inputs\RecommendGoodsPageInput;
use Illuminate\Support\Facades\DB;

class GoodsService extends BaseService
{
    public function getGoodsPage(GoodsPageInput $input, $columns=['*'])
    {
        $query = Goods::query()->where('status', 1);
        if (!empty($input->goodsIds) && count($input->goodsIds) != 0) {
            $query = $query->orderByRaw(DB::raw("FIELD(id, " . implode(',', $input->goodsIds) . ") DESC"));
        }
        if (!empty($input->categoryId)) {
            $query->whereHas('categories', function ($q) use ($input) {
                $q->where('category_id', $input->categoryId);
            });
        }
        if ($input->sort != 'id') {
            $query = $query->orderBy($input->sort, $input->order);
        } else {
            $query = $query
                ->orderBy('sales_volume', 'desc')
                ->orderBy('avg_score', 'desc')
                ->orderBy('commission_rate', 'desc')
                ->orderBy('created_at', 'desc');
        }
        return $query->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function search($keywords, GoodsPageInput $input, $columns=['*'])
    {

        $query = Goods::query()->where('status', 1)->where('name', 'like', "%$keywords%");
        if (!empty($input->categoryId)) {
            $query->whereHas('categories', function ($q) use ($input) {
                $q->where('category_id', $input->categoryId);
            });
        }
        if ($input->sort != 'id') {
            $query = $query->orderBy($input->sort, $input->order);
        } else {
            $query = $query
                ->orderBy('sales_volume', 'desc')
                ->orderBy('avg_score', 'desc')
                ->orderBy('commission_rate', 'desc')
                ->orderBy('created_at', 'desc');
        }
        return $query->paginate($input->limit, $columns, 'page', $input->page);

//        $query = Goods::search($keywords)->where('status', 1);
//        if (!empty($input->categoryId)) {
//            $query = $query->where('category_ids', 'like', "%$input->categoryId%");
//        }
//        if (!empty($input->sort)) {
//            $query = $query->orderBy($input->sort, $input->order);
//        } else {
//            $query = $query
//                ->orderBy('sales_volume', 'desc')
//                ->orderBy('avg_score', 'desc')
//                ->orderBy('commission_rate', 'desc')
//                ->orderBy('created_at', 'desc');
//        }
//        return $query->paginate($input->limit,'page', $input->page);
    }

    public function getGoodsOptions($keywords, $columns = ['*'])
    {
        $query = Goods::query()->where('status', 1);
        if (!empty($keywords)) {
            $query = $query->where('name', 'like', "%$keywords%");
        }
        return $query->get($columns);
    }

    public function getGoodsById($id, $columns=['*'])
    {
        return Goods::query()->find($id, $columns);
    }

    public function getOnSaleGoods($id, $columns=['*'])
    {
        return Goods::query()->where('status', 1)->find($id, $columns);
    }

    public function getOnSaleGoodsList($ids, $columns=['*'])
    {
        return Goods::query()->where('status', 1)->whereIn('id', $ids)->get($columns);
    }

    public function getGoodsListByIds($ids, $columns=['*'])
    {
        return Goods::query()->whereIn('id', $ids)->get($columns);
    }

    public function getGoodsList(GoodsListInput $input, $columns=['*'])
    {
        $query = Goods::query();
        if (!empty($input->name)) {
            $query = $query->where('name', 'like', "%$input->name%");
        }
        if (!empty($input->categoryId)) {
            $query->whereHas('categories', function ($q) use ($input) {
                $q->where('category_id', $input->categoryId);
            });
        }
        if (!empty($input->merchantId)) {
            $query = $query->where('merchant_id', $input->merchantId);
        }
        if (!empty($input->status)) {
            $query = $query->where('status', $input->status);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRecommendGoodsList(RecommendGoodsPageInput $input, $columns=['*'])
    {
        $query = Goods::query()->where('status', 1);
        if (count($input->goodsIds) != 0) {
            $query = $query->whereNotIn('id', $input->goodsIds);
        }
        if (count($input->categoryIds) != 0) {
            $query->whereHas('categories', function ($q) use ($input) {
                $q->whereIn('category_id', $input->categoryIds);
            });
        }
        return $query->orderBy('sales_volume', 'desc')
            ->orderBy('avg_score', 'desc')
            ->orderBy('commission_rate', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function reduceStock($id, $number, $selectedSkuIndex = -1)
    {
        $goods = $this->getOnSaleGoods($id);
        if (is_null($goods)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '商品不存在');
        }

        $skuList = json_decode($goods->sku_list);

        if (count($skuList) != 0 && $selectedSkuIndex != -1) {
            $stock = $skuList[$selectedSkuIndex]->stock;
            if ($stock == 0 || $number > $stock) {
                $this->throwBusinessException(CodeResponse::GOODS_NO_STOCK, '所选规格库存不足');
            }
            // 减规格库存
            $skuList[$selectedSkuIndex]->stock = $skuList[$selectedSkuIndex]->stock - $number;
            $goods->sku_list = json_encode($skuList);
        } else {
            if ($goods->stock == 0 || $number > $goods->stock) {
                $this->throwBusinessException(CodeResponse::GOODS_NO_STOCK, '商品库存不足');
            }
        }
        $goods->stock = $goods->stock - $number;
        $goods->sales_volume = $goods->sales_volume + $number;

        return $goods->cas();
    }

    public function addStock($id, $number, $selectedSkuIndex = -1)
    {
        $goods = $this->getOnSaleGoods($id);
        if (is_null($goods)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '商品不存在');
        }

        $skuList = json_decode($goods->sku_list);

        if (count($skuList) != 0 && $selectedSkuIndex != -1) {
            $skuList[$selectedSkuIndex]->stock = $skuList[$selectedSkuIndex]->stock + $number;
            $goods->sku_list = json_encode($skuList);
        }
        $goods->stock = $goods->stock + $number;

        return $goods->cas();
    }

    public function returnStock($id, $number, $selectedSkuIndex = -1)
    {
        $goods = $this->getGoodsById($id);
        if (!is_null($goods)) {
            $skuList = json_decode($goods->sku_list);

            if (count($skuList) != 0 && $selectedSkuIndex != -1) {
                $skuList[$selectedSkuIndex]->stock = $skuList[$selectedSkuIndex]->stock + $number;
                $goods->sku_list = json_encode($skuList);
            }
            $goods->stock = $goods->stock + $number;
            $goods->cas();
        }
        return $goods;
    }

    public function createGoods(GoodsInput $input)
    {
        $goods = Goods::new();
        return $this->updateGoods($goods, $input);
    }

    public function updateGoods(Goods $goods, GoodsInput $input)
    {
        $goods->merchant_id = $input->merchantId;
        $goods->video = $input->video ?? '';
        $goods->cover = $input->cover;
        $goods->activity_cover = $input->activityCover ?? '';
        $goods->image_list = json_encode($input->imageList);
        $goods->detail_image_list = json_encode($input->detailImageList);
        $goods->default_spec_image = $input->defaultSpecImage;
        $goods->name = $input->name;
        $goods->introduction = $input->introduction ?? '';
        $goods->freight_template_id = $input->freightTemplateId;
        $goods->price = $input->price;
        $goods->market_price = $input->marketPrice ?? 0;
        $goods->commission_rate = $input->commissionRate ?? 0;
        $goods->stock = $input->stock;
        $goods->number_limit = $input->numberLimit ?? 0;
        $goods->spec_list = json_encode($input->specList);
        $goods->sku_list = json_encode($input->skuList);
        $goods->delivery_method = $input->deliveryMethod;
        $goods->refund_status = $input->refundStatus;
        $goods->save();

        return $goods;
    }

    public function updateAvgScore($id, $avgScore)
    {
        $goods = $this->getGoodsById($id);
        if (is_null($goods)) {
            $this->throwBusinessException(CodeResponse::NOT_FOUND, '商品不存在');
        }
        $goods->avg_score = $avgScore;
        $goods->save();
        return $goods;
    }

    public function getListByIds(array $goodsIds, $columns = ['*'])
    {
        return Goods::query()->whereIn('id', $goodsIds)->get($columns);
    }

    public function getGoodsListByMerchantId($merchantId, $columns = ['*'])
    {
        return Goods::query()->where('merchant_id', $merchantId)->get($columns);
    }
}
