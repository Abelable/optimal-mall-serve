<?php

namespace App\Services;

use App\Models\VillageSnackGoods;
use App\Utils\Inputs\RegionGoodsListInput;
use App\Utils\Inputs\PageInput;

class VillageSnackGoodsService extends BaseService
{
    public function getGoodsPage(PageInput $input, $columns = ['*'])
    {
        return VillageSnackGoods::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsList($columns = ['*'])
    {
        return VillageSnackGoods::query()->orderBy('sort', 'desc')->get($columns);
    }

    public function getFilterGoodsList(RegionGoodsListInput $input, $columns = ['*'])
    {
        return VillageSnackGoods::query()->whereIn('goods_id', $input->goodsIds)->get($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return VillageSnackGoods::query()->find($id, $columns);
    }

    public function deleteById($id)
    {
        VillageSnackGoods::query()->where('id', $id)->delete();
    }
}
