<?php

namespace App\Services;

use App\Models\NewYearLocalRegion;
use App\Utils\Inputs\PageInput;

class NewYearLocalRegionService extends BaseService
{
    public function getRegionList(PageInput $input, $columns = ['*'])
    {
        return NewYearLocalRegion::query()->orderBy($input->sort, $input->order)->paginate($input->limit, $columns, 'page', $input->page);
    }

    public function getRegionById($id, $columns = ['*'])
    {
        return NewYearLocalRegion::query()->find($id, $columns);
    }

    public function getRegionByName($name, $columns = ['*'])
    {
        return NewYearLocalRegion::query()->where('name', $name)->first($columns);
    }

    public function getRegionOptions($columns = ['*'])
    {
        return NewYearLocalRegion::query()->where('status', 1)->orderBy('sort', 'desc')->get($columns);
    }
}
