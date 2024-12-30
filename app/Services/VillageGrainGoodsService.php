<?php

namespace App\Services;

use App\Models\VillageGrainGoods;
use App\Utils\Inputs\GoodsListInput;
use App\Utils\Inputs\PageInput;

class VillageGrainGoodsService extends BaseService
{
    public function getGoodsPage(PageInput $input, $columns = ['*'])
    {
        return VillageGrainGoods::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsList($columns = ['*'])
    {
        return VillageGrainGoods::query()->orderBy('sort', 'desc')->get($columns);
    }

    public function getFilterGoodsList(GoodsListInput $input, $columns = ['*'])
    {
        return VillageGrainGoods::query()->whereIn('goods_id', $input->goodsIds)->get($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return VillageGrainGoods::query()->find($id, $columns);
    }

    public function deleteById($id, $columns = ['*'])
    {
        VillageGrainGoods::query()->where('id', $id)->delete();
    }
}
