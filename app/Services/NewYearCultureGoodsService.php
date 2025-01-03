<?php

namespace App\Services;

use App\Models\NewYearCultureGoods;
use App\Utils\Inputs\RegionGoodsListInput;
use App\Utils\Inputs\PageInput;

class NewYearCultureGoodsService extends BaseService
{
    public function getGoodsPage(PageInput $input, $columns = ['*'])
    {
        return NewYearCultureGoods::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsList($columns = ['*'])
    {
        return NewYearCultureGoods::query()->orderBy('sort', 'desc')->get($columns);
    }

    public function getFilterGoodsList(RegionGoodsListInput $input, $columns = ['*'])
    {
        return NewYearCultureGoods::query()->whereIn('goods_id', $input->goodsIds)->get($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return NewYearCultureGoods::query()->find($id, $columns);
    }

    public function deleteById($id)
    {
        NewYearCultureGoods::query()->where('id', $id)->delete();
    }
}
