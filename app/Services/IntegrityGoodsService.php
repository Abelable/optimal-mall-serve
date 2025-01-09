<?php

namespace App\Services;

use App\Models\IntegrityGoods;
use App\Utils\Inputs\RegionGoodsListInput;
use App\Utils\Inputs\PageInput;

class IntegrityGoodsService extends BaseService
{
    public function getGoodsPage(PageInput $input, $columns = ['*'])
    {
        return IntegrityGoods::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsList($columns = ['*'])
    {
        return IntegrityGoods::query()->orderBy('sort', 'desc')->get($columns);
    }

    public function getFilterGoodsList(RegionGoodsListInput $input, $columns = ['*'])
    {
        return IntegrityGoods::query()->whereIn('goods_id', $input->goodsIds)->get($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return IntegrityGoods::query()->find($id, $columns);
    }

    public function deleteById($id)
    {
        return IntegrityGoods::query()->where('id', $id)->delete();
    }

    public function deleteByGoodsId($goodsId)
    {
        return IntegrityGoods::query()->where('goods_id', $goodsId)->delete();
    }
}
