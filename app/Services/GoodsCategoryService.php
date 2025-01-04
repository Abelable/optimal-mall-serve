<?php

namespace App\Services;

use App\Models\GoodsCategory;
use App\Utils\CodeResponse;
use App\Utils\Inputs\PageInput;

class GoodsCategoryService extends BaseService
{
    public function createList($goodsId, array $categoryIds)
    {
        foreach ($categoryIds as $categoryId) {
            $goodsCategory = $this->getGoodsCategory($goodsId, $categoryId);
            if (!is_null($goodsCategory)) {
                $this->throwBusinessException(CodeResponse::DATA_EXISTED, '分类数据已存在，请勿重复提交');
            }
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

    public function getListByGoodsIds(array $goodsIds, $columns = ['*'])
    {
        return GoodsCategory::query()->whereIn('goods_id', $goodsIds)->get($columns);
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
