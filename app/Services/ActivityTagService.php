<?php

namespace App\Services;

use App\Models\ActivityTag;
use App\Utils\Inputs\PageInput;

class ActivityTagService extends BaseService
{
    public function getTagList(PageInput $input, $columns = ['*'])
    {
        $query = ActivityTag::query();
        return $query->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getTagById($id, $columns = ['*'])
    {
        return ActivityTag::query()->find($id, $columns);
    }

    public function getTagByName($name, $columns = ['*'])
    {
        return ActivityTag::query()->where('name', $name)->first($columns);
    }

    public function getTagOptions($columns = ['*'])
    {
        return ActivityTag::query()->where('status', 1)->orderBy('sort', 'desc')->get($columns);
    }
}
