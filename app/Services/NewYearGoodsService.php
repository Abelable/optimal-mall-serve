<?php

namespace App\Services;

use App\Models\NewYearGoods;
use App\Utils\Inputs\RegionGoodsListInput;
use App\Utils\Inputs\PageInput;

class NewYearGoodsService extends BaseService
{
    public function getGoodsPage(PageInput $input, $columns = ['*'])
    {
        return NewYearGoods::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsList($columns = ['*'])
    {
        return NewYearGoods::query()->orderBy('sort', 'desc')->get($columns);
    }

    public function getFilterGoodsList(RegionGoodsListInput $input, $columns = ['*'])
    {
        return NewYearGoods::query()->whereIn('goods_id', $input->goodsIds)->get($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return NewYearGoods::query()->find($id, $columns);
    }

    public function deleteById($id, $columns = ['*'])
    {
        NewYearGoods::query()->where('id', $id)->delete();
    }
}
