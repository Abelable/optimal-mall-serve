<?php

namespace App\Services;

use App\Models\LimitedTimeRecruitCategory;
use App\Utils\Inputs\PageInput;

class LimitedTimeRecruitCategoryService extends BaseService
{
    public function getCategoryList(PageInput $input, $columns = ['*'])
    {
        return LimitedTimeRecruitCategory::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getCategoryById($id, $columns = ['*'])
    {
        return LimitedTimeRecruitCategory::query()->find($id, $columns);
    }

    public function getCategoryByName($name, $columns = ['*'])
    {
        return LimitedTimeRecruitCategory::query()->where('name', $name)->first($columns);
    }

    public function getCategoryOptions($columns = ['*'])
    {
        return LimitedTimeRecruitCategory::query()->where('status', 1)->orderBy('sort', 'desc')->get($columns);
    }
}
