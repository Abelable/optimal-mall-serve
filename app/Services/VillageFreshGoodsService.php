<?php

namespace App\Services;

use App\Models\VillageFreshGoods;
use App\Utils\Inputs\GoodsListInput;
use App\Utils\Inputs\PageInput;

class VillageFreshGoodsService extends BaseService
{
    public function getGoodsPage(PageInput $input, $columns = ['*'])
    {
        return VillageFreshGoods::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsList($columns = ['*'])
    {
        return VillageFreshGoods::query()->orderBy('sort', 'desc')->get($columns);
    }

    public function getFilterGoodsList(GoodsListInput $input, $columns = ['*'])
    {
        return VillageFreshGoods::query()->whereIn('goods_id', $input->goodsIds)->get($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return VillageFreshGoods::query()->find($id, $columns);
    }

    public function deleteById($id, $columns = ['*'])
    {
        VillageFreshGoods::query()->where('id', $id)->delete();
    }
}
