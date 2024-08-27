<?php

namespace App\Services;

use App\Models\TodayGoods;
use App\Utils\Inputs\PageInput;

class TodayGoodsService extends BaseService
{
    public function getGoodsPage(PageInput $input, $columns = ['*'])
    {
        return TodayGoods::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsList($columns = ['*'])
    {
        return TodayGoods::query()->get($columns);
    }

    public function getFilterGoodsList(array $goodsIds, $columns = ['*'])
    {
        return TodayGoods::query()->whereIn('goods_id', $goodsIds)->get($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return TodayGoods::query()->find($id, $columns);
    }
}
