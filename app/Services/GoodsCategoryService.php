<?php

namespace App\Services;

use App\Models\GoodsCategory;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class GoodsCategoryService extends BaseService
{
    public function createList($goodsId, array $categoryIds)
    {
        $existingCategoryIds = $this->getListByGoodsId($goodsId)->pluck('category_id')->toArray();
        $categoryIdsToDelete = array_diff($existingCategoryIds, $categoryIds);
        $categoryIdsToAdd = array_diff($categoryIds, $existingCategoryIds);

        if (!empty($categoryIdsToDelete)) {
            $this->deleteList($goodsId, $categoryIdsToDelete);
        }

        if (!empty($categoryIdsToAdd)) {
            $insertData = [];
            foreach ($categoryIdsToAdd as $categoryId) {
                $insertData[] = [
                    'goods_id' => $goodsId,
                    'category_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            GoodsCategory::query()->insert($insertData);
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

    public function getListByGoodsIds(array $goodsIds, $columns = ['*'])
    {
        return GoodsCategory::query()->whereIn('goods_id', $goodsIds)->get($columns);
    }

    public function getGoodsCategory($goodsId, $categoryId, $columns = ['*'])
    {
        return GoodsCategory::query()->where('goods_id', $goodsId)->where('category_id', $categoryId)->first($columns);
    }

    public function deleteList($goodsId, array $categoryIds)
    {
        return GoodsCategory::query()->where('goods_id', $goodsId)->whereIn('category_id', $categoryIds)->delete();
    }

    public function deleteListByGoodsId($goodsId)
    {
        return GoodsCategory::query()->where('goods_id', $goodsId)->delete();
    }
}
