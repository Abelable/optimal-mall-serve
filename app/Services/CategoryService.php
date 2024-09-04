<?php

namespace App\Services;

use App\Models\Category;
use App\Utils\Inputs\PageInput;

class CategoryService extends BaseService
{
    public function getCategoryList(PageInput $input, $columns = ['*'])
    {
        $query = Category::query();
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getCategoryById($id, $columns = ['*'])
    {
        return Category::query()->find($id, $columns = ['*']);
    }

    public function getCategoryByName($name, $columns = ['*'])
    {
        return Category::query()->where('name', $name)->first($columns);
    }

    public function getCategoryOptions($columns = ['*'])
    {
        return Category::query()->where('status', 1)->orderBy('sort', 'desc')->get($columns);
    }
}
