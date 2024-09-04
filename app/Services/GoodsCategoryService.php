<?php

namespace App\Services;

use App\Models\GoodsCategory;
use App\Utils\Inputs\PageInput;

class GoodsCategoryService extends BaseService
{
    public function createList($goodsId, array $categoryIds)
    {
        foreach ($categoryIds as $categoryId) {
            $goodsCategory = GoodsCategory::new();
            $goodsCategory->goods_id = $goodsId;
            $goodsCategory->category_id = $categoryId;
            $goodsCategory->save();
        }
    }

    public function getPage(PageInput $input, $columns = ['*'])
    {
        return GoodsCategory::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getListByGoodsId($goodsId, $columns = ['*'])
    {
        return GoodsCategory::query()->where('goods_id', $goodsId)->get($columns);
    }

    public function getGoodsCategory($goodsId, $categoryId, $columns = ['*'])
    {
        return GoodsCategory::query()->where('goods_id', $goodsId)->where('category_id', $categoryId)->first($columns);
    }

    public function delete($goodsId, $categoryId)
    {
        return GoodsCategory::query()->where('goods_id', $goodsId)->where('category_id', $categoryId)->delete();
    }

    public function deleteListByGoodsId($goodsId)
    {
        return GoodsCategory::query()->where('goods_id', $goodsId)->delete();
    }
}
