<?php

namespace App\Services;

use App\Models\GiftGoods;
use App\Utils\Inputs\GiftGoodsPageInput;
use App\Utils\Inputs\GiftRegionGoodsListInput;

class GiftGoodsService extends BaseService
{
    public function getGoodsPage(GiftGoodsPageInput $input, $columns = ['*'])
    {
        return GiftGoods::query()
            ->where('type', $input->type)
            ->orderBy($input->sort, $input->order)
            ->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsList(array $typeList, $columns = ['*'])
    {
        return GiftGoods::query()->whereIn('type', $typeList)->get($columns);
    }

    public function getFilterGoodsList(GiftRegionGoodsListInput $input, $columns = ['*'])
    {
        return GiftGoods::query()->where('type', $input->type)->whereIn('goods_id', $input->goodsIds)->get($columns);
    }

    public function getGoodsByGoodsId($goodsId, $columns = ['*'])
    {
        return GiftGoods::query()->where('goods_id', $goodsId)->first($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return GiftGoods::query()->find($id, $columns);
    }

    public function deleteByGoodsId($goodsId)
    {
        return GiftGoods::query()->where('goods_id', $goodsId)->delete();
    }
}
