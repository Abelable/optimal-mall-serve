<?php

namespace App\Services;

use App\Models\LimitedTimeRecruitGoods;
use App\Utils\Inputs\CategoryGoodsListInput;
use App\Utils\Inputs\CategoryPageInput;

class LimitedTimeRecruitGoodsService extends BaseService
{
    public function getGoodsPage(CategoryPageInput $input, $columns = ['*'])
    {
        $query = LimitedTimeRecruitGoods::query();
        if (!empty($input->categoryId)) {
            $query->where('category_id', $input->categoryId);
        }
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getGoodsList($categoryId, $columns = ['*'])
    {
        return LimitedTimeRecruitGoods::query()->where('category_id', $categoryId)->get($columns);
    }

    public function getAllGoodsList($columns = ['*'])
    {
        return LimitedTimeRecruitGoods::query()->get($columns);
    }

    public function getFilterGoodsList(CategoryGoodsListInput $input, $columns = ['*'])
    {
        return LimitedTimeRecruitGoods::query()
            ->where('category_id', $input->categoryId)
            ->whereIn('goods_id', $input->goodsIds)
            ->get($columns);
    }

    public function getGoodsById($id, $columns = ['*'])
    {
        return LimitedTimeRecruitGoods::query()->find($id, $columns);
    }

    public function deleteById($id)
    {
        LimitedTimeRecruitGoods::query()->where('id', $id)->delete();
    }

    public function deleteByGoodsId($goodsId)
    {
        LimitedTimeRecruitGoods::query()->where('goods_id', $goodsId)->delete();
    }
}
