<?php

namespace App\Services;

use App\Models\AdvanceGoods;
use App\Utils\Inputs\GoodsListInput;
use App\Utils\Inputs\PageInput;

class AdvanceGoodsService extends BaseService
{
    public function getGoodsPage(PageInput $input, $columns = ['*'])
    {
        return AdvanceGoods::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsList($columns = ['*'])
    {
        return AdvanceGoods::query()->get($columns);
    }

    public function getFilterGoodsList(GoodsListInput $input, $columns = ['*'])
    {
        return AdvanceGoods::query()->whereIn('goods_id', $input->goodsIds)->get($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return AdvanceGoods::query()->find($id, $columns);
    }
}
