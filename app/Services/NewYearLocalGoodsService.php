<?php

namespace App\Services;

use App\Models\NewYearLocalGoods;
use App\Utils\Inputs\RegionGoodsListInput;
use App\Utils\Inputs\RegionPageInput;

class NewYearLocalGoodsService extends BaseService
{
    public function getGoodsPage(RegionPageInput $input, $columns = ['*'])
    {
        $query = NewYearLocalGoods::query();
        if (!empty($input->regionId)) {
            $query->where('region_id', $input->regionId);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsList($regionId, $columns = ['*'])
    {
        return NewYearLocalGoods::query()->where('region_id', $regionId)->get($columns);
    }

    public function getFilterGoodsList(RegionGoodsListInput $input, $columns = ['*'])
    {
        return NewYearLocalGoods::query()
            ->where('region_id', $input->regionId)
            ->whereIn('goods_id', $input->goodsIds)
            ->get($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return NewYearLocalGoods::query()->find($id, $columns);
    }

    public function deleteById($id)
    {
        NewYearLocalGoods::query()->where('id', $id)->delete();
    }
}
