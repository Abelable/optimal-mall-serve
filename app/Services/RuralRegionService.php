<?php

namespace App\Services;

use App\Models\RuralRegion;
use App\Utils\Inputs\PageInput;

class RuralRegionService extends BaseService
{
    public function getRegionList(PageInput $input, $columns = ['*'])
    {
        return RuralRegion::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRegionById($id, $columns = ['*'])
    {
        return RuralRegion::query()->find($id, $columns);
    }

    public function getRegionByName($name, $columns = ['*'])
    {
        return RuralRegion::query()->where('name', $name)->first($columns);
    }

    public function getRegionOptions($columns = ['*'])
    {
        return RuralRegion::query()->where('status', 1)->orderBy('sort', 'desc')->get($columns);
    }
}
