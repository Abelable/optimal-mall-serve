<?php

namespace App\Services;

use App\Models\LimitedTimeRecruitGoods;
use App\Utils\Inputs\GoodsListInput;
use App\Utils\Inputs\RegionPageInput;

class LimitedTimeRecruitGoodsService extends BaseService
{
    public function getGoodsPage(RegionPageInput $input, $columns = ['*'])
    {
        $query = LimitedTimeRecruitGoods::query();
        if (!empty($input->regionId)) {
            $query->where('region_id', $input->regionId);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsList($regionId, $columns = ['*'])
    {
        return LimitedTimeRecruitGoods::query()->where('region_id', $regionId)->get($columns);
    }

    public function getFilterGoodsList(GoodsListInput $input, $columns = ['*'])
    {
        return LimitedTimeRecruitGoods::query()
            ->where('region_id', $input->regionId)
            ->whereIn('goods_id', $input->goodsIds)
            ->get($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return LimitedTimeRecruitGoods::query()->find($id, $columns);
    }
}
