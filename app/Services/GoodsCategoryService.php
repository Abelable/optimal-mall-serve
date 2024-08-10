<?php

namespace App\Services;

use App\Models\GoodsCategory;
use App\Utils\Inputs\PageInput;

class GoodsCategoryService extends BaseService
{
    public function getCategoryList(PageInput $input, $columns = ['*'])
    {
        $query = GoodsCategory::query();
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getCategoryById($id, $columns = ['*'])
    {
        return GoodsCategory::query()->find($id, $columns = ['*']);
    }

    public function getCategoryByName($name, $columns = ['*'])
    {
        return GoodsCategory::query()->where('name', $name)->first($columns);
    }

    public function getCategoryOptions($columns = ['*'])
    {
        return GoodsCategory::query()->where('status', 1)->orderBy('sort', 'desc')->get($columns);
    }

    public function getOptionsByShopCategoryIds(array $shopCategoryIds, $columns = ['*'])
    {
        return GoodsCategory::query()->whereIn('shop_category_id', $shopCategoryIds)->get($columns);
    }
}
