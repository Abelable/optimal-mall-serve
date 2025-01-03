<?php

namespace App\Services;

use App\Models\VillageGiftGoods;
use App\Utils\Inputs\RegionGoodsListInput;
use App\Utils\Inputs\PageInput;

class VillageGiftGoodsService extends BaseService
{
    public function getGoodsPage(PageInput $input, $columns = ['*'])
    {
        return VillageGiftGoods::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsList($columns = ['*'])
    {
        return VillageGiftGoods::query()->orderBy('sort', 'desc')->get($columns);
    }

    public function getFilterGoodsList(RegionGoodsListInput $input, $columns = ['*'])
    {
        return VillageGiftGoods::query()->whereIn('goods_id', $input->goodsIds)->get($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return VillageGiftGoods::query()->find($id, $columns);
    }

    public function deleteById($id, $columns = ['*'])
    {
        VillageGiftGoods::query()->where('id', $id)->delete();
    }
}
